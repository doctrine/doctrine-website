---
title: "PHP 5.3 and Doctrine 2.0 Teaser"
menuSlug: blog
layout: blog-post
authorName: jwage
authorEmail:
categories: []
permalink: /2009/01/26/php-5-3-and-doctrine-2-0-teaser.html
---
We've been really busy around here as you may have noticed. Doctrine 1.1
is about to be released, we just got some much needed new documentation,
we're going to be offering a printed version of the documentation soon!
Something we haven't talked to much about is Doctrine 2.0! Roman has
been quietly working on the next major major version of Doctrine, 2.0.
This version will be taking a big leap by requiring PHP 5.3! The
progress this new version of PHP is so great and it lends itself very
well to the world of Object Relational Mappers.

Doctrine 2.0 Teaser
===================

Here is an early teaser of what one of the class meta data drivers looks
like:

~~~~ {.sourceCode .php}
<?php
namespace Doctrine\Tests\Models\CMS;

/**
* @DoctrineEntity(tableName="cms_articles")
*/
class CmsArticle
{
   /**
    * @DoctrineId
    * @DoctrineColumn(type="integer")
    * @DoctrineIdGenerator("auto")
    */
   public $id;

   /**
    * @DoctrineColumn(type="varchar", length=255)
    */
   public $topic;

   /**
    * @DoctrineColumn(type="varchar")
    */
   public $text;

   /**
    * @DoctrineManyToOne(targetEntity="Doctrine\Tests\Models\CMS\CmsUser",
           joinColumns={"user_id" = "id"})
    */
   public $user;

   /**
    * @DoctrineOneToMany(targetEntity="Doctrine\Tests\Models\CMS\CmsComment", mappedBy="article")
    */
   public $comments;
}

**NOTE**

Notice these few things:

1.) We define the class meta data for the model in the doc block.
This is just one way you will be able to define class meta data.
YAML schema syntax will be possible as well.

2.) The ``CmsArticle`` class is not extending anything! Thats right
in Doctrine 2.0 your domain model won't be imposed on one bit!

3.) The properties on the class are not required to be public, they
can of course be private or protected. Doctrine will not require
you to have a getter and setter methods for every property. It is
totally up to how the user wants to design the class.
~~~~

Doctrine 2.0 is being built around the concept of a UnitOfWork that
keeps track of your objects during a script execution. You just work
with your objects normally and when you commit a UnitOfWork, Doctrine
takes care of synchronizing your mapped objects with the database,
taking care of referential integrity etc. The boundaries of a UnitOfWork
can be defined by the user, typically, however, one script execution
requires only one UnitOfWork. In Doctrine 2.0 we really try to keep
Doctrine out of your domain objects as much as possible, yet providing
you with all the features you need.

* * * * *

PHP 5.3 Benchmarks
==================

While I have been playing with Doctrine 2.0 I did some simple benchmarks
against the Doctrine 1.0 version test suite to see what kind of
performance improvements were made. I was pretty impressed!

These tests are not the best obviously but it does clearly show that
when you run the Doctrine tests under PHP 5.3, it is faster and uses
less memory.

PHP 5.2.8
---------

> \# | Seconds | Memory --- | --------- | ----------------- 1 | 24 |
> 129170.648438 KB 2 | 23 | 129164.078125 KB 3 | 23 | 129176.851562 KB

PHP 5.3.0alpha4-dev
-------------------

> \# | Seconds | Memory --- | --------- | ----------------- 1 | 21 |
> 89858.7421875 KB 2 | 20 | 89864.59765625 KB 3 | 21 | 89867.89453125 KB

The Doctrine test suite uses **31%** less memory and is **17%** faster
when running under PHP 5.3!
