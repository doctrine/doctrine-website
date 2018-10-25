---
title: "Glimpse of Doctrine 2.0"
authorName: jwage
authorEmail:
categories: []
permalink: /2009/05/22/glimpse-of-doctrine-2-0.html
---
As you all probably already know, we have been working on Doctrine 2.0
pretty much since before we released Doctrine 1.0. This effort has been
primarily led by Roman and he has done an excellent job with things so
first a big thanks goes to him.

A few quick facts
=================

-   Doctrine 2.0 will require PHP 5.3
-   Doctrine 2.0 is FAST
-   We will extend the support life of 1.x

We have decided to move forward with Doctrine 2.0 requiring PHP 5.3.
This release is a significant one, both for PHP and Doctrine, so we
decided to continue support for the 1.x series of Doctrine to give the
adoption of PHP 5.3 and Doctrine 2.0 more time.

Hydration Performance
=====================

In Doctrine 2.0 the performance when hydrating data is greatly improved.
The difference in speed is a combination of code changes and the
performance increase from using PHP 5.3.

| Version | Seconds | \# Records | | ---------- | ------------|
------------- | | 1.1 | 4.3435637950897 | 5000 | | 2.0 | 1.4314442552312
| 5000 | | 2.0 | 3.4690098762512 | 10000 |

A few code snippets
===================

Schema Metadata and Models
==========================

In Doctrine 1.1 a `Doctrine_Record` might look something like the
following.

~~~~ {.sourceCode .php}
<?php
class User extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('id', 'integer', null, array(
          'primary' => true,
          'auto_increment' => true
        ));
        $this->hasColumn('username', 'string', 255);
    }
}

**NOTE** Notice how you have to extend the base class and
everything is a public instance. As you probably know this has lots
of negative effects as it imposes properties and methods on your
models.
~~~~

In Doctrine 2.0 this limitation was removed and you no longer need to
extend a base class.

~~~~ {.sourceCode .php}
<?php
/**
 * @DoctrineEntity
 * @DoctrineTable(name="user")
 */
class User
{
    /**
     * @DoctrineId
     * @DoctrineColumn(type="integer")
     * @DoctrineGeneratedValue(strategy="auto")
     */
    public $id;
    /**
     * @DoctrineColumn(type="varchar", length=255)
     */
    public $username;
}
~~~~

Custom Data Types
=================

In Doctrine 2.0 you can write your own custom data types. Here is an
example of what the custom datatype class might look like.

Type Declaration
----------------

~~~~ {.sourceCode .php}
<?php
namespace My\Project\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * My custom datatype.
 */
class MyType extends Type
{
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // return the SQL used to create your column type. To create a portable column type, use the $platform.
    }

    public function convertToPHPValue($value)
    {
        // This is executed when the value is read from the database. Make your conversions here.
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is written to the database. Make your conversions here, optionally using the $platform.
    }
}
~~~~

Adding Custom Type
------------------

You can easily register your custom type with Doctrine like this.

~~~~ {.sourceCode .php}
<?php
// in bootstrapping code

...

use Doctrine\DBAL\Types\Type;

...

// Register my type
Type::addCustomType('mytype', 'My\Project\Types\MyType');
~~~~

Using Custom Type
-----------------

Now in your model definition you can do something like the following.

~~~~ {.sourceCode .php}
<?php
namespace My\Project\Model;

/**
 * @DoctrineEntity
 * ...
 */
class MyEntity
{
    /**
     * @DoctrineColumn(type="mytype")
     */
    private $data;

    // ... other properties and code
}
~~~~

This is only a small glimpse of what is possible in Doctrine 2.0. You
will start to see more posts on the blog related to Doctrine 2.0 in the
next several months so stay tuned.
