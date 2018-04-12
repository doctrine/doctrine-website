---
title: Write your own ORM on top of Doctrine2
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: []
permalink: /2010/07/19/your-own-orm-doctrine2.html
---
**NOTE**

> The Doctrine ActiveEntity Extension is just an experiment, nothing
> that will be developed much further from the Doctrine Dev Team. It is
> only a show-case for what is possible with Doctrine2. Please feel free
> to take the code and develop it further.

Did you feel the urge to write your own Object-Relational Mapper after
reading Martin Fowlers PoEAA? I am guilty to have tried implementing two
different ORMs on my own, both now safely dumped into the trash.

In isolation each ORM pattern is easy to describe, understand and even
implement. However the combination of a large set of patterns into a
single implementation introduces a lot of hard to solve complexity in
your code. Even simple Object-Relational-Mappers require a lot of
patterns to become useful: Metadata Mapping, Identity Map, Foreign Key
Mapping, Association Table Mapping and Query Object. Implementations
with more features at least need the UnitOfWork and probably many more,
for example handling inheritance, locking, value objects and such.

Doctrine2 already solves a lot of the head aching problems in a
consistent approach. We have been working on this project for almost 2
years now, with all the experience we gained implementing Doctrine 1.
Additionally we make use of well-understood concepts from other ORM
implementations across various languages.

We as developers think that Doctrine2 responsibilities are very well
separated such that you can exchange larger parts of the Doctrine2 core
without having to re-implement everything. So if you ever feel inspired
to implement your own ORM, we would be happy to offer you Doctrine2 as a
foundation to build upon.

There are examples of other ORMs that have taken the re-use instead of
re-implement road. For example the [Groovy Grails
ORM](http://www.grails.org/GORM) is an ActiveRecord implementation on
top of the popular Hibernate Java ORM. Since Groovy is a
java-virtual-machine language it can safely use the Hibernate ORM as a
dependent library.

This article will describe some possible extensions and show where you
can hook into the Doctrine2 core to implement your own ORM. The article
will be very code focused and also comes with a [Github
project](http://github.com/beberlei/Doctrine-ActiveEntity) where all the
code and some tests are hosted.

Doctrine2 and ActiveRecord
==========================

Doctrine2 is implementing the DataMapper pattern, however many
programmers think ActiveRecord is better for various reasons. For me
data-mappers are superiour to ActiveRecord, however I do understand why
ActiveRecord is so popular: Its very easy to get started and do cool
stuff with it! If you want Doctrine2 to be ActiveRecord you can have it.
Actually it is very easy to turn it into a powerful ActiveRecord
implementation, keeping all the powerful features such as DQL.

Some while ago Jonathan already released his approach, called the
"ActiveEntity" extension. Its a single abstract php class that your
entities have to implement, the code is still [in our SVN
repository](http://trac.doctrine-project.org/browser/extensions/ActiveEntity/branches/2.0-1.0/DoctrineExtensions/ActiveEntity.php).
However a more recent version of this code is available as [a project on
Github](http://github.com/beberlei/Doctrine-ActiveEntity). I won't
support this experiment any further, I hope somebody picks it up and
starts maintaining it.

With Jonathans old code, to allow active record entities, you have to
bootstrap the ActiveEntity by passing a static EntityManager:

~~~~ {.sourceCode .php}
<?php
\DoctrineExtensions\ActiveEntity::setEntityManager($em);
~~~~

Now say we have a User Entity (using Jonathans old ActiveEntity):

~~~~ {.sourceCode .php}
<?php
namespace Entities;

use DoctrineExtensions\ActiveEntity;

/** @Entity */
class User extends ActiveEntity
{
    /** @Id @GeneratedValue @column(type="integer") */
    private $id;

    /** @Column(type="string") */
    private $name;
}
~~~~

With PHP 5.3 late-static binding functionality we can now access the
`EntityRepository`, a finder object for entities using a Ruby on
Rails'ish notation:

~~~~ {.sourceCode .php}
<?php
$user = User::find($id);
$users = User::findBy(array("name" => "beberlei"));
$beberlei = User::findOneBy(array("name" => "beberlei"));
~~~~

The code to allow this functionality is very simple:

~~~~ {.sourceCode .php}
<?php
public static function __callStatic($method, $arguments)
{
    return call_user_func_array(array(self::$_em->getRepository(get_called_class()), $method), $arguments);
}
~~~~

There are also some additional methods on the `ActiveEntity` class that
use magic **get and**set and \_\_call methods to access the private
properties of an Entity (such as the User id and name shown above).
Additionally you can call save() or remove() on any instance.

For starters this offers a great ActiveRecord implementation with all
the powerful features that Doctrine2 offers, such as DQL and UnitOfWork.
However we can still go much further:

-   Eliminate the need to define ActiveEntity properties by metadata
    mapping inference
-   Adding your own powerful Metadata Mapping Layer
-   Add a Doctrine 1.2 behaviour system using the PHP 5.3.99DEV Traits
    functionalitiy
-   Add validation to properties of an ActiveEntity

Lets begin with a simple introduction to the Doctrine Metadata Model to
explain how this is all possible.

Doctrine2 Metadata Model
========================

You probably already saw that Doctrine2 offers many different metadata
configuration mechanisms: Annotations, YAML, XML and plain PHP. Any one
of this implementations will transform into an instance of
`Doctrine\ORM\ClassMetadata` which is then cached for subsequent web
requests. The `ClassMetadataFactory` is responsible for creating and
managing those metadata instances.

Doctrine2 uses the `ClassMetadata` instance internally for all runtime
access to your entities metadata, which means that you have to extend
this class such that it works exactly the same from the outside.

If you wanted to extend the inner workings of Doctrine2, this is indeed
the way to go. First extend the EntityManager to replace the
`ClassMetadataFactory` used. This piece of code is the only hackish
workaround, everything else is rather nice :-)

~~~~ {.sourceCode .php}
<?php
namespace DoctrineExtensions\ActiveEntity;

use DoctrineExtensions\ActiveEntity\Mapping\ClassMetadataFactory;

class ActiveEntityManager extends \Doctrine\ORM\EntityManager
{
    protected function __construct(Connection $conn, Configuration $config, EventManager $eventManager)
    {
        parent::__construct($conn, $config, $eventManager);

        $metadataFactory = new ActiveClassMetadataFactory($this);
        $metadataFactory->setCacheDriver($this->getConfiguration()->getMetadataCacheImpl());

        // now this is the only hack required to get it work:
        $reflProperty = new \ReflectionProperty('Doctrine\ORM\EntityManager', 'metadataFactory');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this, $metadataFactory);
    }

    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        // ... copy paste from EntityManager::create()

        return new ActiveEntityManager($conn, $config, $conn->getEventManager());
    }
}
~~~~

And both the `ClassMetadataFactory` and `ClassMetadata`:

~~~~ {.sourceCode .php}
<?php
namespace DoctrineExtensions\ActiveEntity\Mapping;

class ActiveClassMetadataFactory extends \Doctrine\ORM\Mapping\ClassMetadataFactory
{
    protected function _newClassMetadataInstance($className)
    {
        return new ActiveClassMetadata($className);
    }
}

class ActiveClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata
{
}
~~~~

This is the foundation of your own Doctrine2-based ORM. We will see in
the next section how we can use this.

Exchange Doctrine2 Reflection for Array-based Field Storage
===========================================================

Doctrine2 uses reflection to access the current values of an entity.
This is necessary, because Doctrine2 is a Data Mapper that enforces
clean separation between entities and persistence. If we extend it to be
an ActiveRecord implementation this separation is not wanted anymore and
we can opt for a new approach, using the get()/set() methods on our
ActiveEntities.

Defining the properties "id" and "name" will then not be necessary
anymore, they will all be saved in an array hash-map called "\_data"
inside the ActiveEntity. We cannot use annotations for metadata anymore,
however the XML or YAML drivers would still work smoothly.

To get started we have to modify our `ActiveClassMetadata` a bit to
exchange the contents of reflClass and reflFields with our own classes.
Looking at the `ClassMetadata` code and doing some project wide searches
I found out about all the necessary changes. To replace the
`ReflectionClass` we only need to exchange `getProperty` and keep the
rest. To exchange `ReflectionProperty` we only have to overwrite
`setAccessible()`, `getValue()` and `setValue()`.

~~~~ {.sourceCode .php}
<?php
namespace DoctrineExtensions\ActiveEntity\Reflection;

class ActiveEntityReflectionClass extends \ReflectionClass
{
    public function getProperty($name)
    {
        return new ActiveEntityPropertyReflection($this->name, $name);
    }
}

class ActiveEntityReflectionProperty
{
    public $name = null;
    public $class = null;

    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function setAccessible($flag) {}

    public function setValue($entity = null, $value = null)
    {
        $entity->set($this->name, $value);
    }

    public function getValue($entity = null)
    {
        return $entity->get($this->name);
    }
}
~~~~

This is about enough to exchange reflection transformation against a
simple ActiveRecord get/set approach. Now we need to replace the all the
instantiations of `ReflectionClass` relevant for runtime mapping with
our implementation:

~~~~ {.sourceCode .php}
<?php
namespace DoctrineExtensions\ActiveEntity\Mapping;

use DoctrineExtensions\ActiveEntity\Reflection\ActiveEntityReflectionClass;
use DoctrineExtensions\ActiveEntity\Reflection\ActiveEntityReflectionProperty;

class ActiveClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata
{
    public function __construct($entityName)
    {
        parent::__construct($entityName);
        $this->reflClass = new ActiveEntityReflectionClass($entityName);
        $this->namespace = $this->reflClass->getNamespaceName();
        $this->table['name'] = $this->reflClass->getShortName();
    }

    /**
     * Restores some state that can not be serialized/unserialized.
     *
     * @return void
     */
    public function __wakeup()
    {
        // lots of code here, see the Github Repository
    }
}
~~~~

Again, this is enough and our ActiveEntity Mapping now works. We can
heavily modify the `ActiveEntity` now to loose the requirement to
specify properties for the defined metadata. We can rewrite the User
entity to be:

~~~~ {.sourceCode .php}
<?php
namespace Entities;

use DoctrineExtensions\ActiveEntity\ActiveEntity;

class User extends ActiveEntity
{
}
~~~~

Using an XML or YAML Mapping is already enough for this ActiveEntity to
work out of the box.

Implementing your own Metadata Mapping Driver
=============================================

In the spirit of Doctrine 1.\* or GORM there should be a PHP based
metadata mapping driver now and actually Doctrine2 ships with one
already:

~~~~ {.sourceCode .php}
<?php
$config = new \Doctrine\ORM\Configuration();
$config->setMetadataDriverImpl(new \Doctrine\ORM\Mapping\Driver\StaticPHPDriver());
// ...
~~~~

This allows to specify the metadata within the User class:

~~~~ {.sourceCode .php}
<?php
namespace Entities;

use DoctrineExtensions\ActiveEntity\ActiveEntity;
use DoctrineExtensions\ActiveEntity\Mapping\ActiveClassMetadata;

class User extends ActiveEntity
{
    static public function loadMetadata(ActiveClassMetadata $cm)
    {
        // work with $cm here!
    }
}
~~~~

You could extend that Static PHP Driver even more for the next section.
We could add additional metadata information, such as names of
behaviours to extend or validators or anything else.

Using Traits for Behaviours
===========================

We want to add a simple "Timestampable" behaviour now, hooking into the
`loadClassMetadata` event [as described in the
documentation](http://www.doctrine-project.org/projects/orm/2.0/docs/reference/events/en#load-classmetadata-event):

Now this is untested code, as i don't have a PHP-5.3.99-DEV version
compiled at this machine.

The following trait can be used by our `User` entity:

~~~~ {.sourceCode .php}
<?php
namespace DoctrineExtensions\ActiveEntity\Behaviour;

trait Timestampable
{
    public function created()
    {
        return $this->get('created');
    }

    public function updated()
    {
        return $this->get('updated');
    }

    /** will be a prePersist lifecycle hook */
    public function setCreated()
    {
        return $this->set('created', new \DateTime("now"));
    }

    /** will be a preUpdate lifecycle hook */
    public function setUpdated()
    {
        return $this->set('updated', new \DateTime("now"));
    }
}

class User extends ActiveEntity use Timestampable
{

}
~~~~

We now need an Event that modifies the `ActiveClassMetadata` as
required:

~~~~ {.sourceCode .php}
<?php
namespace DoctrineExtensions\ActiveEntity\Behaviour;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class TimestampableEvent
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $traits = $classMetadata->reflClass->getTraitNames();
        if (!in_array("DoctrineExtensions\ActiveEntity\Behaviour\Timestampable", $traits)) {
            return;
        }

        $classMetadata->mapField(array(
            'type' => 'datetime',
            'fieldName' => 'created',
        ));
        $classMetadata->mapField(array(
            'type' => 'datetime',
            'fieldName' => 'updated',
        ));
        $classMetadata->addLifecycleCallback("prePersist", "setCreated");
        $classMetadata->addLifecycleCallback("prePersist", "setUpdated");
        $classMetadata->addLifecycleCallback("preUpdate", "setUpdated");
    }
}
~~~~

You can now register this behaviour with your Entity Manager and just
the usage of the trait `Timestampable` adds two additional fields and
updates them accordingly.

> **NOTE**
>
> Again, the trait code is untested. It should work, but I cannot
> guarantee! :)

Conclusion
==========

What are you waiting for? This article showed a very deep modification
of the Doctrine2 core to turn it into Active Record. The changes
required some understanding of the inner workings of Doctrine2, however
not many changes were required in the end.

[See the code on
GitHub!](http://github.com/beberlei/Doctrine-ActiveEntity)
