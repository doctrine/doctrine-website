---
layout: default
title: About
---

.. raw:: html
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ site.url }}/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About</li>
        </ol>
    </nav>

The Doctrine Project is the home of a selected set of PHP libraries primarily focused
on providing persistence services and related functionality. Its prize projects are a
Object Relational Mapper and the Database Abstraction Layer it is built on top of.
You can read more about what Doctrine has to offer below.

Common Shared Libraries
~~~~~~~~~~~~~~~~~~~~~~~

`doctrine/common <http://github.com/doctrine/common>`_

Doctrine Common contains some base functionality and interfaces you need
in order to create a Doctrine style object mapper. All of our mapper
projects follow the same ``Doctrine\Common\Persistence`` interfaces.
Here are the ``ObjectManager`` and ``ObjectRepository`` interfaces:

.. code-block:: php

    <?php

    namespace Doctrine\Common\Persistence;

    interface ObjectManager
    {
        public function find($className, $id);
        public function persist($object);
        public function remove($object);
        public function merge($object);
        public function clear($objectName = null);
        public function detach($object);
        public function refresh($object);
        public function flush();
        public function getRepository($className);
    }

    interface ObjectRepository
    {
        public function find($id);
        public function findAll();
        public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
        public function findOneBy(array $criteria);
    }

`doctrine/collections <http://github.com/doctrine/collections>`_

Doctrine Collections is a library that contains classes for working with
arrays of data. Here is an example using the simple
``Doctrine\Common\Collections\ArrayCollection`` class:

.. code-block:: php

    <?php

    $data = new \Doctrine\Common\Collections\ArrayCollection(array(1, 2, 3));
    $data = $data->filter(function($count) { return $count > 1; });

`doctrine/annotations <http://github.com/doctrine/annotations>`_

Doctrine Annotations is a library that allows you to parse structured
information out of a doc block.

Imagine you have a class with a doc block like the following:

.. code-block:: php

    <?php

    /** @Foo(bar="value") */
    class User
    {

    }

You can parse the information out of the doc block for ``User`` easily.
Define a new annotation object:

.. code-block:: php

    <?php

    /**
     * @Annotation
     * @Target("CLASS")
     */
    class Foo
    {
        /** @var string */
        public $bar;
    }

Now you can get instances of ``Foo`` defined on the ``User``:

.. code-block:: php

    <?php

    $reflClass = new ReflectionClass('User');
    $reader = new \Doctrine\Common\Annotations\AnnotationReader();
    $classAnnotations = $reader->getClassAnnotations($reflClass);

    foreach ($classAnnotations AS $annot) {
        if ($annot instanceof Foo) {
            echo $annot->bar; // prints "value";
        }
    }

`doctrine/inflector <http://github.com/doctrine/inflector>`_

Doctrine Inflector is a library that can perform string manipulations
with regard to upper/lowercase and singular/plural forms of words.

.. code-block:: php

    <?php

    $camelCase = 'camelCase';
    $table = \Doctrine\Common\Inflector::tableize($camelCase);
    echo $table; // camel_case

`doctrine/lexer <http://github.com/doctrine/lexer>`_

Doctrine Lexer is a library that can be used in Top-Down, Recursive
Descent Parsers. This lexer is used in Doctrine Annotations and in
Doctrine ORM (DQL).

Here is what the ``AbstractLexer`` provided by Doctrine looks like:

.. code-block:: php

    <?php

    namespace Doctrine\Common\Lexer;

    abstract class AbstractLexer
    {
        public function setInput($input);
        public function reset();
        public function resetPeek();
        public function resetPosition($position = 0);
        public function isNextToken($token);
        public function isNextTokenAny(array $tokens);
        public function moveNext();
        public function skipUntil($type);
        public function isA($value, $token);
        public function peek();
        public function glimpse();
        public function getLiteral($token);

        abstract protected function getCatchablePatterns();
        abstract protected function getNonCatchablePatterns();
        abstract protected function getType(&$value);
    }

To implement a lexer just extend the
``Doctrine\Common\Lexer\AbstractLexer`` class and implement the
``getCatchablePatterns``, ``getNonCatchablePatterns``, and ``getType``
methods. Here is a very simple example lexer implementation named
``CharacterTypeLexer``. It tokenizes a string to ``T_UPPER``,
``T_LOWER`` and ``T_NUMER``:

.. code-block:: php

    <?php

    use Doctrine\Common\Lexer\AbstractLexer;

    class CharacterTypeLexer extends AbstractLexer
    {
        const T_UPPER =  1;
        const T_LOWER =  2;
        const T_NUMBER = 3;

        protected function getCatchablePatterns()
        {
            return array(
                '[a-bA-Z0-9]',
            );
        }

        protected function getNonCatchablePatterns()
        {
            return array();
        }

        protected function getType(&$value)
        {
            if (is_numeric($value)) {
                return self::T_NUMBER;
            }

            if (strtoupper($value) === $value) {
                return self::T_UPPER;
            }

            if (strtolower($value) === $value) {
                return self::T_LOWER;
            }
        }
    }

Use ``CharacterTypeLexer`` to extract an array of upper case characters:

.. code-block:: php

    <?php

    class UpperCaseCharacterExtracter
    {
        private $lexer;

        public function __construct(CharacterTypeLexer $lexer)
        {
            $this->lexer = $lexer;
        }

        public function getUpperCaseCharacters($string)
        {
            $this->lexer->setInput($string);
            $this->lexer->moveNext();

            $upperCaseChars = array();
            while (true) {
                if (!$this->lexer->lookahead) {
                    break;
                }

                $this->lexer->moveNext();

                if ($this->lexer->token['type'] === CharacterTypeLexer::T_UPPER) {
                    $upperCaseChars[] = $this->lexer->token['value'];
                }
            }

            return $upperCaseChars;
        }
    }

    $upperCaseCharacterExtractor = new UpperCaseCharacterExtracter(new CharacterTypeLexer());
    $upperCaseCharacters = $upperCaseCharacterExtractor->getUpperCaseCharacters('1aBcdEfgHiJ12');

    print_r($upperCaseCharacters);

The variable ``$upperCaseCharacters`` contains all of the upper case
characters:

.. code-block:: php

    Array
    (
        [0] => B
        [1] => E
        [2] => H
        [3] => J
    )

`doctrine/cache <http://github.com/doctrine/cache>`_

Doctrine Cache is a library that provides an interface for caching data.
It comes with implementations for some of the most popular caching data
stores. Here is what the ``Cache`` interface looks like:

.. code-block:: php

    <?php

    namespace Doctrine\Common\Cache;

    interface Cache
    {
        function fetch($id);
        function contains($id);
        function save($id, $data, $lifeTime = 0);
        function delete($id);
        function getStats();
    }

Here is an example using memcache:

.. code-block:: php

    <?php

    $memcache = new \Memcache();
    $cache = new \Doctrine\Common\Cache\MemcacheCache();
    $cache->setMemcache($memcache);

    $cache->set('key', 'value');

    echo $cache->get('key') // prints "value"

Other supported drivers are:

-  APC
-  Couchbase
-  Filesystem
-  Memcached
-  MongoDB
-  PhpFile
-  Redis
-  Riak
-  WinCache
-  Xcache
-  ZendData

Database Abstraction Layers
---------------------------

`doctrine/dbal <http://github.com/doctrine/dbal>`_

Doctrine DBAL is a library that provides an abstraction layer for
relational databases in PHP. Read `Doctrine DBAL: PHP Database
Abstraction
Layer <http://jwage.com/post/31080076112/doctrine-dbal-php-database-abstraction-layer>`_
blog post for more information on the DBAL.

.. code-block:: php

    <?php

    $config = new \Doctrine\DBAL\Configuration();
    //..
    $connectionParams = array(
        'dbname' => 'mydb',
        'user' => 'user',
        'password' => 'secret',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    );
    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

    $articles = $conn->fetchAll('select * from articles');

    $count = $conn->executeUpdate('UPDATE user SET username = ? WHERE id = ?', array('jwage', 1));

    $conn->insert('user', array('username' => 'jwage'));

    $conn->update('user', array('username' => 'jwage'), array('id' => 1));

    $qb = $conn->createQueryBuilder()
        ->select('u.id')
        ->addSelect('p.id')
        ->from('users', 'u')
        ->leftJoin('u', 'phonenumbers', 'u.id = p.user_id');

    $results = $qb->getQuery()->execute();

`doctrine/mongodb <http://github.com/doctrine/mongodb>`_

Doctrine MongoDB is a library that provides an abstraction layer on top
of the `PHP MongoDB PECL extension <http://pecl.php.net/package/mongo>`_. It provides some additional
functionality and abstractions to make working with MongoDB easier.

.. code-block:: php

    <?php

    $conn = new \Doctrine\MongoDB\Connection();
    $database = $conn->selectDatabase('dbname');
    $collection = $database->selectCollection('collname');

    $qb = $collection->createQueryBuilder()
        ->field('username')->equals('jwage')
        ->field('status')->in(array('active', 'test'));

    $user = $qb->getQuery()->getSingleResult();

`doctrine/couchdb-client <http://github.com/doctrine/couchdb-client>`_

Doctrine CouchDB Client is a library that provides a connection
abstraction to CouchDB by wrapping around the CouchDB HTTP API.

.. code-block:: php

    <?php

    $client = \Doctrine\CouchDB\CouchDBClient::create();

    array($id, $rev) = $client->postDocument(array('foo' => 'bar'));
    $client->putDocument(array('foo' => 'baz'), $id, $rev);

    $doc = $client->findDocument($id);

Object Mappers
--------------

The object mappers are where all the pieces come together. The object
mappers provide transparent persistence for PHP objects. As mentioned
above, they all implement the common interfaces from ``Doctrine\Common``
so working with each of them is generally the same. You have an
``ObjectManager`` to manage the persistent state of your domain objects:

.. code-block:: php

    <?php

    $user = new User();
    $user->setId(1);
    $user->setUsername('jwage');

    $om = $this->getYourObjectManager();
    $om->persist($user);
    $om->flush(); // insert the new document

Then you can find that object later and modify it:

.. code-block:: php

    <?php

    $user = $om->find('User', 1);
    echo $user->getUsername(); // prints "jwage"

    $user->setUsername('jonwge'); // change the obj in memory

    $om->flush(); // updates the object in the database

Check out one of the supported object mappers below:

- `ORM <http://github.com/doctrine/doctrine2>`_
- `CouchDB ODM <http://github.com/doctrine/couchdb-odm>`_
- `MongoDB ODM <http://github.com/doctrine/mongodb-odm>`_
- `PHPCR ODM <http://github.com/doctrine/phpcr-odm>`_
- `OrientDB ODM <http://github.com/doctrine/orientdb-odm>`_
