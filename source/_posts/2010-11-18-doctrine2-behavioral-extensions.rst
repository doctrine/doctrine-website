---
title: Doctrine2 Behavioral Extensions
authorName: gmorkevicius 
authorEmail: 
categories: []
indexed: false
---
These behavioral extensions will give you another view on Doctrine2
capabilities handling behaviors through the EventListeners. These
extensions operate like some of the most commonly used behaviors,
leaving the domain objects as clean as possible. Annotations makes
it easy to understand an intended behavior of properties on your
Entities.


.. raw:: html

   <div style="float: left; width: 450px;">
       <div style="padding: 15px; border: 1px solid #ccc; margin: 0 15px; background: #eee; color: #888">
       <p style="margin: 0;">
   
Hi, my name is Gediminas Morkevicius, I have 4 year experience in
C++ and PHP 5 and I'm very keen on new technologies and Doctrine2
is one of them. Development is not only a job for me, but it is my
lifestyle and I'm pleased to give some love back to Doctrine2.

.. raw:: html

   </p>
       <p style="margin: 10px 0 0;">
   
I would like to thank Doctrine2 and Symfony2 teams for these
wonderful projects. They have great potential.

.. raw:: html

   </p>
       </div>
   </div>
   
    **NOTE** This blog entry relates to Doctrine2 Beta4 ORM version and
    could possibly be outdated depending on what the current version of
    Doctrine2 is at the time of you reading this post. Then integrating
    these extensions on your project it is recommended to use latest
    Doctrine2 library packages, because where are most recent updates
    used on extensions for metadata caching and annotations.
    
.. raw:: html

       <div style="clear:both"></div>
       

Content:


-  Introduction on behavioral extensions
-  Setup and autoloading
-  Translatable extension
-  Tree extension
-  Sluggable extension
-  Timestampable extension
-  All nested together

First of all, this post intends to give an example on how
"behaviors" can be implemented through the Doctrine2
EventListeners. All these behavioral extensions can be nested and
support flush operation which can include lots of update, insert
and remove actions. This is the most common issue when behavior
development process is started.

Furthermore, all extensions are mapped by annotations consequently
leaving domain objects clean from interfaces and their methods.
Plus, the performance speed is even greater if cache driver is
used, because metadata for single Entity is mapped and validated
only once.

Before we begin exploring the extensions, I\`m glad to mention that
these extensions are already available on Symfony2 Bundle ported by
Christophe Coevoet.

## Setting up the autoloader and listeners

First of all, download this library from public github repository
and setup the autoloading for extensions:

.. code-block:: php

    <?php
    $classLoader = new \Doctrine\Common\ClassLoader('Gedmo', "/path/to/library/DoctrineExtensions/lib");
    $classLoader->register();

Translatable behavior will need additional annotation driver for
Translation Entity metadata. The example below illustrates the
chain driver implementation:

.. code-block:: php

    <?php
    $chainDriverImpl = new \Doctrine\ORM\Mapping\Driver\DriverChain();
    $yourDefaultDriverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver('/yml/mapping/files'); // only an example
    $translatableDriverImpl = $doctrineOrmConfig->newDefaultAnnotationDriver(
        '/path/to/library/DoctrineExtensions/lib/Gedmo/Translatable/Entity'
    );
    $chainDriverImpl->addDriver($yourDefaultDriverImpl, 'Entity');
    $chainDriverImpl->addDriver($translatableDriverImpl, 'Gedmo\Translatable');
    $doctrineOrmConfig->setMetadataDriverImpl($chainDriverImpl);

Attaching the Event Listeners on the event manager
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $evm = new \Doctrine\Common\EventManager();
    // timestampable
    $evm->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
    // sluggable
    $evm->addEventSubscriber(new \Gedmo\Sluggable\SluggableListener());
    // tree
    $evm->addEventSubscriber(new \Gedmo\Tree\TreeListener());
    // translatable
    $translationListener = new \Gedmo\Translatable\TranslationListener();
    $translationListener->setTranslatableLocale('en_us');
    // in real world app the locale should be loaded from session, example:
    // Session::getInstance()->read('locale');
    $evm->addEventSubscriber($translationListener);
    // now this event manager should be passed to entity manager constructor

    **NOTE** It is recommended to attach TranslationListener as the
    last whereas sluggable extension must create a slug before
    translating it.


## Translatable

Translatable behavior offers a very handy solution for translating
specific record fields into different languages. Furthermore, it
loads the translations automatically for the currently used locale.
Locale can be set by TranslationListener during it's initialization
or later. It also leaves the possibility to force a specific locale
directly on the Entity itself.

Feature list:


-  Translates all records automatically when object hydration is
   used
-  Supports a separate translation table for each Entity
-  There can be a default locale specified, which would force
   entity to leave its original translation in default locale.

Translatable annotations:
~~~~~~~~~~~~~~~~~~~~~~~~~


-  @gedmo:Translatable indicates that the column is translatable
-  @gedmo:TranslationEntity(class="my") this class annotation tells
   to use specified Entity to store translations
-  @gedmo:Locale or @gedmo:Language indicates that the column must
   not be mapped and that it may be used to override
   TranslationListener\`s locale

Translatable Entity example:

.. code-block:: php

    <?php
    namespace Entity;
    
    /**
     * @Entity
     */
    class Article
    {
        /**
         * @Id
         * @GeneratedValue
         * @Column(type="integer")
         */
        private $id;
    
        /**
         * @gedmo:Translatable
         * @Column(type="string", length=128)
         */
        private $title;
    
        /**
         * @gedmo:Translatable
         * @Column(type="text")
         */
        private $content;
    
        /**
         * @gedmo:Locale
         */
        private $locale;
    
        public function getId()
        {
            return $this->id;
        }
    
        public function setTitle($title)
        {
            $this->title = $title;
        }
    
        public function getTitle()
        {
            return $this->title;
        }
    
        public function setContent($content)
        {
            $this->content = $content;
        }
    
        public function getContent()
        {
            return $this->content;
        }
    
        public function setTranslatableLocale($locale)
        {
            $this->locale = $locale;
        }
    }

There is no need for any additional operations while working with
Translatable Entities. All processing is done by event listener,
just like in good old behaviors. Except that in Doctrine2 the code
is simpler and easy to understand and you may inspect it and
customize if you see any point in doing that.

Here are standard usage examples, the locale was set to "en\_us" on
listener:

.. code-block:: php

    <?php
    $article = new \Entity\Article;
    $article->setTitle('my title in en');
    $article->setContent('my content in en');
    $em->persist($article);
    $em->flush();

This inserted an article and populated the translations for it in
"en\_us" locale. Now lets translate it into another language:

.. code-block:: php

    <?php
    // first load the article
    $article = $em->find('Entity\Article', 1 /*article id*/);
    $article->setTitle('my title in de');
    $article->setContent('my content in de');
    $article->setTranslatableLocale('de_de'); // change locale
    $em->persist($article);
    $em->flush();

This updated an article and inserted the translations for it in
"de\_de" locale. The TranslationRepository gives some handy methods
on retrieving all translations:

.. code-block:: php

    <?php
    $em->clear(); // ensure the cache is clean
    $article = $em->find('Entity\Article', 1 /*article id*/);
    $repository = $em->getRepository('Gedmo\Translatable\Entity\Translation');
    $translations = $repository->findTranslations($article);
    /* $translations contains:
    Array (
        [de_de] => Array
            (
                [title] => my title in de
                [content] => my content in de
            )
    
        [en_us] => Array
            (
                [title] => my title in en
                [content] => my content in en
            )
    )*/
    // the locale now is "en_us" and current article::title in db is "my title in de"
    echo $article->getTitle();
    // prints: "my title in en" because it loads the translation automatically

Using the "default locale":
~~~~~~~~~~~~~~~~~~~~~~~~~~~

In some cases we need the default translation as a fallback if
record does not have a translation on globally used locale. In that
case TranslationListener uses the current value of Entity. But
there is a way to specify a default locale which would force Entity
to keep
it``s field value on default locale. And if record has already been translated in this locale, the record will not update it``s
value, only insert a new translation into translation table. You
can specify the default locale on TranslationListener\`s
initialization:

.. code-block:: php

    <?php
    $translationListener->setDefaultLocale('en_us');

Using a diferent Translation Entity for translation storage:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In some cases when there are thousands of records we would like to
have a single table for translations of this Entity in order to
increase the performance on translation loading speed. The example
below will show how to specify a diferent Entity for your
translations by extending the translation mapped superclass. Thanks
to Christophe Coevoet for the idea on translation abstraction.

.. code-block:: php

    <?php
    namespace Entity\Translation;
    
    use Gedmo\Translatable\Entity\AbstractTranslation;
    
    /**
     * @Table(name="article_translations", indexes={
     *      @index(name="article_translation_idx", columns={"locale", "entity", "foreign_key", "field"})
     * })
     * @Entity(repositoryClass="Gedmo\Translatable\Repository\TranslationRepository")
     */
    class ArticleTranslation extends AbstractTranslation
    {
        /**
         * All required columns are mapped through inherited superclass
         */
    }

This Entity will be used instead of default Translation Entity only
if we specify a class annotation
@gedmo:TranslationEntity(class="Entity"). Now lets slightly modify
our Article Entity:

.. code-block:: php

    <?php
    /**
     * @Entity
     * @gedmo:TranslationEntity(class="Entity\Translation\ArticleTranslation")
     */
    class Article
    {
        // ...
    }

Now all translations of Article will be stored and queried from a
specific table.

## Tree

Tree behavior is not a Nested Set which it was in the first version
of Doctrine. This one does not require any TreeManager nor
NodeWrapper and it does not support multiple roots on tree because
it is meant to be simple and is implemented through the event
listener. All standard Tree operations are accessible through
TreeNodeRepository which is advisable to be used for Tree
structured Entities. This Tree allows all traverse operations to be
done on your nodes. When performance or advanced customizations
becomes an issue, a more advanced implementation like nested-set by
Brandon Turner might be needed.

Tree annotations:


-  @gedmo:TreeLeft identifies the column as storage of Tree left
   value
-  @gedmo:TreeRight identifies the column as storage of Tree right
   value
-  @gedmo:TreeParent this will identify this column as a ManyToOne
   relation of parent node

All these annotations are required for the Tree to be functional.
And here is an example of a simple Tree Entity:

.. code-block:: php

    <?php
    namespace Entity;
    
    /**
     * use repository for handy tree functions
     * @Entity(repositoryClass="Gedmo\Tree\Repository\TreeNodeRepository")
     */
    class Category
    {
        /**
         * @Column(type="integer")
         * @Id
         * @GeneratedValue
         */
        private $id;
    
        /**
         * @Column(length=64)
         */
        private $title;
    
        /**
         * @gedmo:TreeLeft
         * @Column(name="lft", type="integer")
         */
        private $lft;
    
        /**
         * @gedmo:TreeRight
         * @Column(name="rgt", type="integer")
         */
        private $rgt;
    
        /**
         * @gedmo:TreeParent
         * @ManyToOne(targetEntity="Category", inversedBy="children")
         */
        private $parent;
    
        /**
         * @OneToMany(targetEntity="Category", mappedBy="parent")
         * @OrderBy({"lft" = "ASC"})
         */
        private $children;
    
        public function getId()
        {
            return $this->id;
        }
    
        public function setTitle($title)
        {
            $this->title = $title;
        }
    
        public function getTitle()
        {
            return $this->title;
        }
    
        public function setParent(Category $parent)
        {
            $this->parent = $parent;
        }
    
        public function getParent()
        {
            return $this->parent;
        }
    }

Basic usage example:

.. code-block:: php

    <?php
    $food = new Entity\Category();
    $food->setTitle('Food');
    
    $fruits = new Entity\Category();
    $fruits->setTitle('Fruits');
    $fruits->setParent($food);
    
    $vegetables = new Entity\Category();
    $vegetables->setTitle('Vegetables');
    $vegetables->setParent($food);
    
    $carrots = new Entity\Category();
    $carrots->setTitle('Carrots');
    $carrots->setParent($vegetables);
    
    $em->persist($food);
    $em->persist($fruits);
    $em->persist($vegetables);
    $em->persist($carrots);
    $em->flush();

The result after flush will generate the tree of food chain :)

::

    /food (1-8)
        /fruits (2-3)
        /vegetables (4-7)
            /carrots (5-6)

Using TreeNodeRepository functions:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $repo = $em->getRepository('Entity\Category');
    
    $food = $repo->findOneByTitle('Food');
    echo $repo->childCount($food);
    // prints: 3
    echo $repo->childCount($food, true/*direct*/);
    // prints: 2
    $children = $repo->children($food);
    // $children contains:
    // 3 nodes
    $children = $repo->children($food, false, 'title');
    // will sort the children by title
    $carrots = $repo->findOneByTitle('Carrots');
    $path = $repo->getPath($carrots);
    /* $path contains:
       0 => Food
       1 => Vegetables
       2 => Carrots
    */
    
    // verification and recovery of tree
    $repo->verify();
    $em->clear(); // ensures cache clean
    // can return TRUE if tree is valid, or array of errors found on tree
    $repo->recover();
    $em->clear(); // ensures cache clean
    // if tree has errors it will try to fix all tree nodes
    
    // single node removal
    $vegies = $repo->findOneByTitle('Vegitables');
    $repo->removeFromTree($vegies);
    // it will remove this node from tree and reparent all children
    
    // reordering the tree
    $repo->reorder(null/*reorder starting from parent*/, 'title');
    $em->clear(); // ensures cache clean
    // it will reorder all tree node left-right values by the title
    
    // moving up and down the nodes, by changing their (left, right) values
    $carrots = $repo->findOneByTitle('Carrots');
    $repo->moveUp($carrots, 1/*by one position*/);
    // carrots now should be at the top in it`s level
    $repo->moveDown($carrots, true/*to bottom*/);
    // carrots now should be at the bottom in it`s level

After using such Tree operations like: reorder, recover, verify it
is recommended to clear the EntityManager cache since it may have
cached nodes with old left and right values. This would be an issue
if you plan on using nodes during the same request after mentioned
operations. And if you need some custom functions on your Node
repository - simply extend the TreeNodeRepository.

## Sluggable

Sluggable behavior will build the slug from annotated fields on a
chosen slug field which should store the generated slug. Slugs can
be unique and styled. Currently this extension does not support
unique constraint on slug field in cases when there are many
inserts on a single flush operation, because it cannot issue a
query to ensure uniqueness. Use a simple index instead.

Sluggable annotations:
~~~~~~~~~~~~~~~~~~~~~~


-  @gedmo:Sluggable all columns identified by this annotation will
   be included in a slug
-  @gedmo:Slug this column will be used to store the generated
   slug


.. code-block:: php

    <?php
    namespace Entity;
    
    /**
     * @Entity
     */
    class Article
    {
        /** @Id @GeneratedValue @Column(type="integer") */
        private $id;
    
        /**
         * @gedmo:Sluggable
         * @Column(name="title", type="string", length=64)
         */
        private $title;
    
        /**
         * @gedmo:Sluggable
         * @Column(name="code", type="string", length=16)
         */
        private $code;
    
        /**
         * @gedmo:Slug
         * @Column(name="slug", type="string", length=128, unique=true)
         */
        private $slug;
    
        public function getId()
        {
            return $this->id;
        }
    
        public function setTitle($title)
        {
            $this->title = $title;
        }
    
        public function getTitle()
        {
            return $this->title;
        }
    
        public function setCode($code)
        {
            $this->code = $code;
        }
    
        public function getCode()
        {
            return $this->code;
        }
    
        public function getSlug()
        {
            return $this->slug;
        }
    }

Basic usage example:

.. code-block:: php

    <?php
    $article = new Entity\Article();
    $article->setTitle('the title');
    $article->setCode('my code');
    $em->persist($article);
    $em->flush();
    
    echo $article->getSlug();
    // prints: the-title-my-code

### Some other configuration options:

.. raw:: html

   

-  updatable (optional, default=true) - true to update the slug on
   sluggable field changes, false - otherwise
-  unique (optional, default=true) - true if slug should be unique
   and if identical it will be prefixed, false - otherwise
-  separator (optional, default="-") - separator which will
   separate words in slug
-  style (optional, default="default") - "default" all letters will
   be lowercase, "camel" - first letter will be uppercase


.. code-block:: php

    <?php
    // diferent slug configuration example
    class Article
    {
        // ...
        /**
         * @gedmo:Slug(style="camel", separator="_", updatable=false, unique=false)
         * @Column(name="slug", type="string", length=128, unique=true)
         */
        private $slug;
        // ...
    }
    
    // result would be: The_Title_My_Code

## Timestampable

Timestampable behavior will automate the update of date fields on
your Entities. It works through annotations and can update fields
on creation, update or even on specific internal or related Entity
property change.

Timestampable annotations:
~~~~~~~~~~~~~~~~~~~~~~~~~~


-  @gedmo:Timestampable this annotation specifies that this column
   is timestampable, by default it updates this column on general
   update. If column is not (date, datetime or time) it will trigger
   an exception. Bellow are listed available configuration options:

Available configuration options:


-  on - is the main option and can be: create, update or change.
   This option indicates when an update should be triggered
-  field - only valid if on="change" is specified, tracks property
   for changes
-  value - only valid if on="change" is specified, if tracked field
   has the specified value when it triggers an update


.. code-block:: php

    <?php
    namespace Entity;
    
    /**
     * @Entity
     */
    class Article
    {
        /** @Id @GeneratedValue @Column(type="integer") */
        private $id;
    
        /**
         * @Column(type="string", length=128)
         */
        private $title;
    
        /**
         * @var datetime $created
         *
         * @gedmo:Timestampable(on="create")
         * @Column(type="date")
         */
        private $created;
    
        /**
         * @var datetime $updated
         *
         * @Column(type="datetime")
         * @gedmo:Timestampable(on="update")
         */
        private $updated;
    
        public function getId()
        {
            return $this->id;
        }
    
        public function setTitle($title)
        {
            $this->title = $title;
        }
    
        public function getTitle()
        {
            return $this->title;
        }
    
        public function getCreated()
        {
            return $this->created;
        }
    
        public function getUpdated()
        {
            return $this->updated;
        }
    }

## All nested together

.. code-block:: php

    <?php
    namespace Entity;
    
    /**
     * @Entity(repositoryClass="Gedmo\Tree\Repository\TreeNodeRepository")
     */
    class Category
    {
        /**
         * @Column(name="id", type="integer")
         * @Id
         * @GeneratedValue
         */
        private $id;
    
        /**
         * @gedmo:Translatable
         * @gedmo:Sluggable
         * @Column(length=64)
         */
        private $title;
    
        /**
         * @gedmo:TreeLeft
         * @Column(type="integer")
         */
        private $lft;
    
        /**
         * @gedmo:TreeRight
         * @Column(type="integer")
         */
        private $rgt;
    
        /**
         * @gedmo:TreeParent
         * @ManyToOne(targetEntity="Category", inversedBy="children")
         */
        private $parent;
    
        /**
         * @OneToMany(targetEntity="Category", mappedBy="parent")
         * @OrderBy({"lft" = "ASC"})
         */
        private $children;
    
        /**
         * @gedmo:Translatable
         * @gedmo:Slug(style="camel", separator="_")
         * @Column(length=128)
         */
        private $slug;
    
        /**
         * @gedmo:Timestampable(on="create")
         * @Column(type="date")
         */
        private $created;
    
        /**
         * @gedmo:Timestampable(on="update")
         * @Column(type="datetime")
         */
        private $updated;
    
        public function getId()
        {
            return $this->id;
        }
    
        public function setTitle($title)
        {
            $this->title = $title;
        }
    
        public function getTitle()
        {
            return $this->title;
        }
    
        public function setParent(Category $parent)
        {
            $this->parent = $parent;
        }
    
        public function getParent()
        {
            return $this->parent;
        }
    
        public function getCreated()
        {
            return $this->created;
        }
    
        public function getUpdated()
        {
            return $this->updated;
        }
    
        public function getSlug()
        {
            return $this->slug;
        }
    }

After running some inserts you will get the expected result. Don\`t
be afraid to use concurrent flush with many inserts and updates or
even remove operations, everything is meant to work fine.

Some of you may think that using no interface takes longer to check
Entities on events. In fact, it takes only a single 'if' statement
and a cache check on first request. This way the process is much
cleaner.

Maybe these extensions will help some of you realize how clean
domain objects can be and how well the model represents itself.
It's much more convenient than Active Record - browsing several
extended classes, going through magic methods of those classes.
While here you see everything in one grasp.

There will be updates on my blog page and new articles which may
interest some of you. You can give some love back by forking a
repository and creating an ODM Document support on extensions or
suggesting me an idea of improvements or maybe an issue which you
have detected.
