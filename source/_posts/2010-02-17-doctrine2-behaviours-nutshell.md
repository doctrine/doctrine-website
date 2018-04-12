---
title: Doctrine 2 "Behaviours" in a Nutshell
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: []
permalink: /2010/02/17/doctrine2-behaviours-nutshell.html
---
**NOTE** This blog entry relates to an outdated Doctrine 2 Alpha
:   version. Please see the documentation for the most up to date
    behavior.

One of the most common fallacies out there about Doctrine 2 abandoning
Behaviours is that developers now have to implement fancy logic to
re-implement them yourself. Doctrine 2's approach to completly separate
ORM from your domain classes allows to build behaviours in a very clean,
unobstrusive and simple object-oriented way. This article shows you how
to implement some of the Doctrine 1 behaviours in your Doctrine 2 code.
For this I will rewrite the Doctrine 1.x manuals examples for each
Behaviour.

This example uses Annotations as example, yet this of course works with
YAML and XML mappings. Additionally Doctrine 2 allows constructors to
have required arguments as of a commit of the last week. This allows for
some pretty slick enforcements in user-land code as you will see in this
post.

Straightforward combination of "Behaviours"
===========================================

All the code listed below is somehwat more verbose than the Doctrine 1
code, however much more bound to the domain of your model and very
straightforward. There is no magic involved and you will fully
understand what will be happening in each of the behaviours. The best of
all, although not a simple trick, you will be able to combine ALL
behaviours in one model class and still be completly on top of their
inner workings.

Timestampable
=============

Timestampable is a behaviour that requires you to hook into the
pre-update event which is called whenever an entity is updated:

~~~~ {.sourceCode .php}
<?php
/**
 * @Id
 * @HasLifecycleCallbacks
 */
class BlogPost
{
    /**
     * @Column(type="DateTime")
     */
    private $created;

    /**
     * @Column(type="DateTime")
     */
    private $updated;

    public function __construct()
    {
        // constructor is never called by Doctrine
        $this->created = $this->updated = new DateTime("now");
    }

    /**
     * @PreUpdate
     */
    public function updated()
    {
        $this->updated = new DateTime("now");
    }
}
~~~~

Sluggable
=========

The sluggable behaviour is trivial to implement in Doctrine 2:

~~~~ {.sourceCode .php}
<?php
class BlogPost
{
    /** @Column(type="string") */
    private $slug;

    /** @Column(type="string") */
    private $title;

    public function setTitle($title)
    {
        if ($this->slug == null) {
            $this->slug = MyStringHelper::slugize($title);
        }
        $this->title = $title;
    }

    /**
     * Put this method in if your slug should be "editable"
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}
~~~~

See how its much more explicit in your code how and why the slug is
generated.

NestedSet
=========

This is one of the more complex behaviours in Doctrine 1 and it won't be
necessarily more easy in Doctrine 2. However as this is an important
feature we will provide an implementation as a `DoctrineExtensions`
namespaced package that will be maintained by Doctrine Devs.

Searchable
==========

There is currently no plan to port the Searchable behaviour to Doctrine
2, but the possibility to instantiate objects using *new* allows a very
simple integration of a Doctrine 2 model with Apache Solr or Lucene with
a little wrapper that re-creates detached instances from this powerful
search engines.

For example using
[ezcSearch](http://ezcomponents.org/docs/api/trunk/introduction_Search.html)
we can make our BlogPost accessible for Solr:

~~~~ {.sourceCode .php}
<?php
class BlogPost implements ezcBasePersistable, ezcSearchDefinitionProvider 
{
    public function getState()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'slug' => $this->slug,
        );
    }

    public function setState($state)
    {
        foreach ($state AS $k => $v) {
            $this->$k = $v;
        }
    }

    static public function getDefinition() 
    {
        // define search schema
        return $def;
    }
}
~~~~

ezcSearch can then index a blog post whenever it is changed by hooking
an EventListener into the Doctrine `PreUpdate` Event:

~~~~ {.sourceCode .php}
<?php
class EzcSearchListener
{
    private $_searchSession;

    public function __construct(ezcSearchSession $searchSession)
    {
        $this->_searchSession = $searchSession;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof ezcBasePersistable) {
            $this->_searchSession->index($args->getEntity());
        }
    }
}
~~~~

You can now hook this event into Doctrine's EntityManager:

~~~~ {.sourceCode .php}
<?php
$searchListener = new EzcSearchListener(...);
$em->getEventManager()->addEventListener(
    array(Doctrine\ORM\Events::preUpdate), $searchListener
);
~~~~

Now when you search for your entities you get returned `BlogPost`
instances from ezcSearchs Solr interface:

~~~~ {.sourceCode .php}
<?php
// initialize a pre-configured query
$q = $session->createFindQuery( 'BlogPost' );
$searchWord = 'test';

// where either body or title contains thr $searchWord
$q->where(
    $q->lOr(
        $q->eq( 'body', $searchWord ),
        $q->eq( 'title', $searchWord )
    )
);
$searchedBlogPosts = $session->find( $q ); 
~~~~

These instances are detached from the EntityManager when they get
returned from ezcSearch and can be merged back into the persistence
context:

~~~~ {.sourceCode .php}
<?php
$searchedBlogPosts[0]->setTitle("ChangeFoo");
$em->merge($searchedBlogPosts[0]);
~~~~

Read about Merging, Detached instances and other cool stuff of Doctrines
object model in the [Working with
Objects](http://www.doctrine-project.org/documentation/manual/2_0/en/working-with-objects#merging-entities)
chapter of the manual.

Versionable
===========

By default Doctrine 2 comes with a way to set a *version* column that is
automatically incremented on each update. Using the event system it is
easy to use this information to implement a versionable audit-log
behaviour. The required code is more verbose than the simple
configuration of Doctrine 1, however there is much less magic involved
and you can implement this behaviour in a way that is trivial to
understand for someone new looking at your code:

~~~~ {.sourceCode .php}
<?php
/**
 * @Entity
 * @HasLifeCycleCallbacks
 * @generatedValue(strategy="AUTO")
 */
class BlogPost
{
    /**
     * @Id
     * @Column(type="integer")
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $title;

    /**
     * @Column(type="text")
     */
    private $body;

    /**
     * @Column(type="integer")
     * @version
     */
    private $version;

    /**
     * @OneToMany(targetEntity="BlogPostVersion", mappedBy="post")
     */
    private $auditLog = array();

    /**
     * @PrePersist
     * @PreUpdate
     */
    public function logVersion()
    {
        $this->auditLog[] = new BlogPostVersion($this);  
    }
    // getters
}

/**
 * @Entity
 */
class BlogPostVersion
{
    /**
     * @Id
     * @Column(type="integer")
     * @generatedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $title;

    /**
     * @Column(type="text")
     */
    private $body;

    /**
     * @Column(type="integer")
     */
    private $version;

    /**
     * @ManyToOne(targetEntity="BlogPost")
     */
    private $post;

    public function __construct(BlogPost $post)
    {
        $this->post = $post;
        $this->title = $post->getTitle();
        $this->body = $post->getBody();
        $this->version = $post->getCurrentVersion();       
    }
}
~~~~

I18N
====

Multi-Language content is an important topic and can be implemented in
Doctrine 2, since its just a fancy name for a One-To-Many relation.
However currently Doctrine 2 does not allow to persist keys by name,
which makes a OneToMany implementation a bit more intensive then it
could be. We plan to implement primitive value collections however which
would simplify any attempt to implement nested structured content, that
is not an entity by itself.

Soft Delete
===========

We won't support soft-delete at all. If you want to implement a
soft-delete alike behaviour its probably a good idea to look into the
State pattern instead.

Blameable
=========

Implementing this behaviour is just a matter of adding two fields
*createdByUserId* and *modifiedByUserId* fields and setting them
whenever one of your relevant fields change by hooking into setter
methods:

~~~~ {.sourceCode .php}
<?php
/**
 * @Entity
 */
class BlogPost
{
    /**
     * @Column(type="string")
     */
    private $title;

    /**
     * @Column(type="integer")
     */
    private $modifiedByUserId;

    public function updateBlogPost($title, ..., User $user)
    {
        $this->title = $title;
        $this->modifiedByUserId = $user->getId();
    }
}
~~~~

Sortable
========

Same as I18N, we are planning to support persistence of collection keys
in the Doctrine 2 Core. This would allow to sort collections by using
the possibilities of the `Doctrine\Common\Collections\Collection`
interface.

Conclusion
==========

Although slightly more complex than Doctrine 1s simple configuration
options, most "behaviours" are still way easy to implement in Doctrine
2. The additional benefit of this straightforward approach: *You can
combine behaviours in any way, inside your domain model, without having
to wonder how the magic works together, you are completly on top of it.*
