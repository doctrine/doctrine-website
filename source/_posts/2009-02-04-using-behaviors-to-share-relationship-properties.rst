---
title: Using Behaviors to Share Relationship Properties
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
Define The Schema
-----------------

In this article we will demonstrate some more ways to add
functionality to Doctrine by using the behavior system. We will
call this behavior ``SharedProperties`` and it allows you to share
properties between your models and one-to-one relationships. Here
is an example schema that will make use of the behavior we will
write.

::

    [yml]
    Entity:
      actAs:
        Sluggable:
          fields: [name]
        Timestampable:
      columns:
        name: string(255)
        is_active:
          type: boolean
          default: false
    
    BlogPost:
      actAs:
        SharedProperties:
          relations: [Entity]
      columns:
        title: string(255)
        body: clob
    
    User:
      actAs:
        SharedProperties:
          relations: [Entity]
      columns:
        username: string(255)
        password: string(255)
    
    Administrator:
      actAs:
        SharedProperties:
          relations: [User]
      columns:
        responsibilities: string(255)

The schema should be somewhat self-explanatory. Each model that
acts as ``SharedProperties`` you must specify an array of model
names or existing relationship aliases. Our behavior will
automatically add foreign keys for the list of models and
instantiate a one-to-one relationship between the models
automatically.

Write the Template
------------------

Lets write the first part of our template that simply adds a column
for each of the listed ``relations``:

.. code-block:: php

    <?php
    class SharedProperties extends Doctrine_Template
    {
        protected $_options = array();
    
        public function __construct($options)
        {
            $this->_options = $options;
        }
    
        public function setTableDefinition()
        {
            foreach ($this->_options['relations'] as $relation) {
                $columnName = Doctrine_Inflector::tableize($relation) . '_id';
                if (!$this->_table->hasColumn($columnName)) {
                    $this->hasColumn($columnName, 'integer');
                }
            }
        }
    }

    **NOTE** You will notice we add columns for each of the
    ``relations`` specified if the column does not already exist. We
    will use these columns to automatically create the
    relationships/foreign keys between the models if they don't already
    exist in the next step.


Enhance the Template
--------------------

Now lets enhance our template and add a ``setUp()`` method to
instantiate our relationships between the list of ``relations`` and
the columns we added in the previous step:

.. code-block:: php

    <?php
    class SharedProperties extends Doctrine_Template
    {
        // ...
    
        public function setUp()
        {
            foreach ($this->_options['relations'] as $model) {
                $table = $this->_table;
                $local = Doctrine_Inflector::tableize($model) . '_id';
                $foreign = Doctrine::getTable($model)->getIdentifier();
                $this->_makeRelation($table, $model, $local, $foreign, true);
            }
    
            foreach ($this->_options['relations'] as $model) {
                $table = Doctrine::getTable($model);
                $local = $table->getIdentifier();
                $foreign = Doctrine_Inflector::tableize($model) . '_id';
                $this->_makeRelation($table, $this->_table->getOption('name'), $table->getIdentifier(), $foreign);
            }
        }
    
        protected function _makeRelation(Doctrine_Table $table, $model, $local, $foreign, $cascade = false)
        {
            if (!$table->hasRelation($model)) {
                $options = array('local'   => $local, 'foreign' => $foreign);
                if ($cascade) {
                    $options['onDelete'] = 'CASCADE';
                }
                $table->bind(array($model, $options), Doctrine_Relation::ONE);
            }
        }
    }

Generated SQL
-------------

This code we've added now makes a one-to-one relationship between
the models that act as ``SharedProperties`` and the list of models
specified. So for example, ``Entity`` has one ``BlogPost`` and
``BlogPost`` has one ``Entity``. The above models at this point
would generate the following SQL:

::

    [sql]
    CREATE TABLE administrator (id BIGINT AUTO_INCREMENT, responsibilities VARCHAR(255), user_id BIGINT, INDEX user_id_idx (user_id), PRIMARY KEY(id)) ENGINE = INNODB;
    
    CREATE TABLE blog_post (id BIGINT AUTO_INCREMENT, title VARCHAR(255), body LONGTEXT, entity_id BIGINT, INDEX entity_id_idx (entity_id), PRIMARY KEY(id)) ENGINE = INNODB;
    
    CREATE TABLE entity (id BIGINT AUTO_INCREMENT, name VARCHAR(255), is_active TINYINT(1) DEFAULT '0', slug VARCHAR(255), created_at DATETIME, updated_at DATETIME, UNIQUE INDEX sluggable_idx (slug), PRIMARY KEY(id)) ENGINE = INNODB;
    
    CREATE TABLE user (id BIGINT AUTO_INCREMENT, username VARCHAR(255), password VARCHAR(255), entity_id BIGINT, INDEX entity_id_idx (entity_id), PRIMARY KEY(id)) ENGINE = INNODB;
    
    ALTER TABLE administrator ADD FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE;
    
    ALTER TABLE blog_post ADD FOREIGN KEY (entity_id) REFERENCES entity(id) ON DELETE CASCADE;
    
    ALTER TABLE user ADD FOREIGN KEY (entity_id) REFERENCES entity(id) ON DELETE CASCADE;

Sharing Properties/Methods
--------------------------

Now to get to the fun, the main purpose of doing all this is to
share the properties of these relationships. We can accomplish this
by using the ``Doctrine_Record_Filter`` feature and some magic PHP
``__call()`` functionality. First lets modify our template to
attach a new record filter.

    **TIP** Records filters in Doctrine allow you to handle all unknown
    properties access on a Doctrine object. This allows us to forward
    the calls on to the relationships so you can access properties from
    them.


.. code-block:: php

    <?php
    class SharedProperties extends Doctrine_Template
    {
        // ...
    
        public function setTableDefinition()
        {
            // ...
    
            $this->_table->unshiftFilter(new SharedPropertiesFilter($this->_options));
        }
    
        // ...
    }

Now that we have attached our filter we need to write that class:

.. code-block:: php

    <?php
    class SharedPropertiesFilter extends Doctrine_Record_Filter
    {
        protected $_options = array();
    
        public function __construct($options)
        {
            $this->_options = $options;
        }
    
        public function init()
        {
            foreach ($this->_options['relations'] as $model) {
                $this->_table->getRelation($model);
            }
        }
    
        public function filterSet(Doctrine_Record $record, $name, $value)
        {
            foreach ($this->_options['relations'] as $model) {
                try {
                    $record->$model->$name = $value;
                    return $record;
                } catch (Exception $e) {}
            }
            throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
        }
    
        public function filterGet(Doctrine_Record $record, $name)
        {
            foreach ($this->_options['relations'] as $model) {
                try {
                    return $record->$model->$name;
                } catch (Exception $e) {}
            }
            throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
        }
    }

Now you can see this filter checks to see if the property exists on
any of the ``relations`` specified otherwise throws the normal
``Doctrine_Record_UnknownPropertyException``.

The last thing we need to do is add a magic ``__call()`` function
to our template to handle the forwarding of any unknown methods to
the ``relations``:

.. code-block:: php

    <?php
    class SharedProperties extends Doctrine_Template
    {
        // ...
    
        public function __call($method, $arguments)
        {
            $invoker = $this->getInvoker();
            foreach ($this->_options['relations'] as $model) {
                try {
                    return call_user_func_array(array($invoker->$model, $method), $arguments);
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }

This is required if we have functions defined on the models and
want to be able to access these methods. So for example if we were
to add a ``setPassword()`` method to the generated ``User`` class
like the following:

.. code-block:: php

    <?php
    class User extends BaseUser
    {
        public function setPassword($password)
        {
            $this->_set('password', md5($password));
        }
    }

Without the above ``__call()`` function we would not be able to do
the following:

.. code-block:: php

    <?php
    $administrator = new Administrator();
    $administrator->setPassword('new_password');

    **TIP** **Auto Accessor and Mutator Overriding**

    If you want Doctrine to automatically override accessors with
    matching ``set*()`` and ``get*()`` functions then you need to
    enable the ``auto_accessor_override`` attribute in your
    configuration where you create your connections and set Doctrine
    attributes:

.. code-block:: php

    <?php
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute('auto_accessor_override', true);

    Now with that attribute the following is possible. Instead of
    having to call the method ``setPassword()``, Doctrine sees you are
    setting the ``password`` and a method named ``setPassword()``
    exists so it uses it to do the mutating.

.. code-block:: php

    <?php
        $administrator->password = 'new_password';


Example Usage
-------------

That is it! Our behavior is implemented and we are ready to write
some code that use our new models.

Creating New Records
~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $admin = new Administrator();
    $admin->name = 'Jonathan H. Wage';
    $admin->username = 'jwage';
    $admin->password = 'changeme';
    $admin->is_active = 1;
    $admin->responsibilities = 'Train all the PHP developers!';
    $admin->save();

Now that code results in the following structure being persisted to
the database:

.. code-block:: php

    <?php
    print_r($admin->toArray(true));
    /*
    Array
    (
        [id] => 2
        [responsibilities] => Train all the PHP developers!
        [user_id] => 2
        [User] => Array
            (
                [id] => 2
                [username] => jwage
                [password] => 4cb9c8a8048fd02294477fcb1a41191a
                [entity_id] => 3
                [Entity] => Array
                    (
                        [id] => 3
                        [name] => Jonathan H. Wage
                        [is_active] => 1
                        [slug] => jonathan-h-wage
                        [created_at] => 2009-02-04 16:01:12
                        [updated_at] => 2009-02-04 16:01:12
                    )
    
            )
    
    )
    */

Data Fixtures
~~~~~~~~~~~~~

Similarly, the following data fixtures would be possible:

::

    [yml]
    BlogPost:
      BlogPost_1:
        name: Test Blog Post
        title: This is a test blog post
        body: This is a test blog post
    
    Administrator:
      Administrator_1:
        name: Test Manager
        username: jwage
        password: changeme
        responsibilities: Overseeing development department

Querying For and Accessing Data
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can query for these relationships as well:

.. code-block:: php

    <?php
    $q = Doctrine_Query::create()
        ->from('Administrator a')
        ->leftJoin('a.User u')
        ->leftJoin('u.Entity e')
        ->where('u.username = ?', 'jwage');
    
    $user = $q->fetchOne();
    echo $user['created_at'];

The above code would output the value of the ``created_at`` column
that actually exists in the ``Entity`` model that is available
through the ``Administrator->User->Entity`` relations.
