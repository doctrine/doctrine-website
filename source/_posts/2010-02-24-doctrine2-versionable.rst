---
title: A re-usable Versionable Behavior for Doctrine 2
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: []
indexed: false
---
**NOTE** This blog entry relates to an outdated Doctrine 2 Alpha
    version. Please see the documentation for the most up to date
    behavior. A test-implementation for this behavior is on
    github.com/beberlei/DoctrineExtensions


My previous post on behaviors in Doctrine 2 generated quite some
discussion about the difference on behaviours that are re-usable
across models and the trivial specific implementations I have
shown.

In this post I will show a re-usable versionable (audit-log)
behavior. For this we will need the following ingredients:


-  An interface ``DoctrineExtensions\Versionable\Versionable``
-  A class ``DoctrineExtensions\Versionable\VersionManager``
-  An event listener
   ``DoctrineExtensions\Versionable\VersionListener``
-  A generic entity
   ``DoctrineExtensions\Versionable\ResourceVersion``

    **NOTE** The Event API is currently in the central focus of our
    efforts so the API shown here may change before the first Beta
    release.


The workflow is as follows, each Entity that is supposed to be
versionable has to implement the interface ``Versionable`` which
looks like this:

.. code-block:: php

    <?php
    namespace DoctrineExtensions\Versionable;
    
    interface Versionable
    {
        /**
         * @return int
         */
        public function getCurrentVersion();
    
        /**
         * @return array
         */
        public function getVersionedData();
    
        /**
         * @return int
         */
        public function getResourceId();
    }

Whenever an entity is persisted or updated the state that is
persisted will also be logged in an audit table. The state is
returned with an array of key value pairs in the
``getVersionedData()`` and the current version has to be the value
of the @Version column of the entity.

To sum up, the requirements of an entity that can be a
``Versionable`` in this simple implementation:


-  Single Integer Primary Key.
-  Has to be versioned with an integer column.

How does such versioned data look like? The generic resource
version entity looks like this. Its a Doctrine Entity, but in a
domain model its an immutable value object. It should not be
changed after creation.

.. code-block:: php

    <?php
    namespace DoctrineExtensions\Versionable;
    
    class ResourceVersion
    {
        /** @Id @Column(type="integer") */
        private $id;
    
        /** @Column(type="string") */
        private $resourceName;
    
        /** @Column(type="integer") */
        private $resourceId;
    
        /** @Column(type="array") */
        private $versionedData;
    
        /**
         * @Column(type="integer") */
        private $version;
    
        /** @Column(type="datetime") */
        private $snapshotDate;
    
        public function __construct(Versionable $resource)
        {
            $this->resourceName = get_class($resource);
            $this->resourceId = $resource->getResourceId();
            $this->versionedData = $resource->getVersionedData();
            $this->version = $resource->getCurrentVersion();
            $this->snapshotDate = new \DateTime("now");
        }
    
        // getters
    }

Now we need to solve the problem of generating the
``ResourceVersion`` whenever an ``Versionable`` entity is persisted
or updated. This can be done by using the
`Doctrine EventManager API <http://www.doctrine-project.org/documentation/manual/2_0/en/events>`_.
We will implement the ``EventSubscriber`` interface and hook into
the "onFlush" event.

.. code-block:: php

    <?php
    namespace DoctrineExtensions\Versionable;
    
    use Doctrine\Common\EventSubscriber,
        Doctrine\ORM\Events,
        Doctrine\ORM\Event\OnFlushEventArgs,
        Doctrine\ORM\EntityManager;
    
    class VersionListener implements EventSubscriber
    {
        public function getSubscribedEvents()
        {
            return array(Events::onFlush);
        }
    
        public function onFlush(OnFlushEventArgs $args)
        {
            $em = $args->getEntityManager();
            $uow = $em->getUnitOfWork();
    
            foreach ($uow->getScheduledEntityInsertions() AS $entity) {
                if ($entity instanceof Versionable) {
                    $this->_makeSnapshot($entity);
                }
            }
    
            foreach ($uow->getScheduledEntityUpdates() AS $entity) {
                if ($entity instanceof Versionable) {
                    $this->_makeSnapshot($entity);
                }
            }
        }
    
        private function _makeSnapshot($entity)
        {
            $resourceVersion = new ResourceVersion($entity);
            $class = $this->_em->getClassMetadata(get_class($resourceVersion));
    
            $this->_em->persist( $resourceVersion );
            $this->_em->getUnitOfWork()->computeChangeSet($class, $resourceVersion);
        }
    }

How do we hook this ``VersionListener`` into the EntityManager? We
will wrap the VersionManager around it that handles registration
and offers some convenience methods to retrieve the versions of a
resource.

.. code-block:: php

    <?php
    namespace DoctrineExtensions\Versionable;
    
    use Doctrine\ORM\EntityManager;
    
    class VersionManager
    {
        private $_em;
    
        public function __construct(EntityManager $em)
        {
            $this->_em = $em;
            $this->_em->getEventManager()->addEventSubscriber(
                new VersionListener()
            );
        }
    
        public function getVersions(Versionable $resource)
        {
            $query = $this->_em->createQuery(
                "SELECT v FROM DoctrineExtensions\Versionable\ResourceVersion v INDEX BY v.version ".
                "WHERE v.resourceName = ?1 AND v.resourceId = ?2 ORDER BY v.version DESC");
            $query->setParameter(1, get_class($resource));
            $query->setParameter(2, $resource->getResourceId());
    
            return $query->getResult();
        }
    }

Now using this to retrieve all the versions of a given entity that
is versionable you would go and:

.. code-block:: php

    <?php
    // $em EntityManager, $blogPost my Blog Post
    
    $versionManager = new VersionManager($em);
    $versions = $versionManager->getVersions($blogPost);
    
    echo "Old Title: ".$versions[$oldVersionNum]->getVersionedData('title');
    
    // Create a new version
    $blogPost->setTitle("My very new title");    
    $em->flush();

This is a first example of how to use the powerful Doctrine 2 Event
API. It is certainly not easy to use, as you need to understand the
inner workings of the UnitOfWork and the different steps it is in
during the flush process. However you can generate huge benefits in
reusability.

The versionable behaviour could be extended by the following
features:


-  Create a new interface ``Revertable`` that extends
   ``Versionable`` and add a method
   ``revert(Revertable $resource, $toVersion)`` to the
   ``VersionManager`` that handles the retrieval, invoking of revert
   and such.
-  Create a new interface Diffable with a method diff($aVersion,
   $bVersion) and new method diff(Diffable $resource, $aId, $bId) to
   the VersionManager that handles the delegation of a difference
   computation between two versions to the Diffable implementor.

Another approach would be not to save the complete state of an
entity during the flush operation, but only the fields that
changed. This is generally called an *AuditLog*. We could add an
``Auditable`` interface much in the same manner than the
``Versionable`` and retrieve the ChangeSets of each entity during
flush using the following event listener:

.. code-block:: php

    <?php
    class AuditListener implements EventSubscriber
    {
        public function getSubscribedEvents()
        {
            return array(Events::onFlush);
        }
    
        public function onFlush(OnFlushEventArgs $args)
        {
            $em = $args->getEntityManager();
            $uow = $em->getUnitOfWork();
    
            $changeDate = new DateTime("now");
            $class = $em->getClassMetadata('DoctrineExtensions\Auditable\AuditEntry');
    
            foreach ($uow->getScheduledEntityUpdates() AS $entity) {
                if ($entity instanceof Auditable) {
                    $changeSet = $uow->getEntityChangeSet($entity);
    
                    foreach ($changeSet AS $field => $vals) {
                        list($oldValue, $newValue) = $vals;
                        $audit = new AuditEntry(
                            $entity->getResourceName(),
                            $entity->getId(),
                            $oldValue,
                            $newValue,
                            $changeDate
                        );
    
                        $em->persist($audit);
                        $em->getUnitOfWork()
                           ->computeChangeSet($class, $audit);
                    }
                }
            }
        }
    }

This approach can also be re-used or combined with several similiar
behaviours, like Taggable, Blamable, Commentable.
