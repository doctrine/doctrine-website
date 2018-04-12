---
title: Doctrine OrientDB Object Document Mapper
menuSlug: blog
authorName: odino 
authorEmail: 
categories: []
permalink: /2012/12/28/doctrine-orientdb-odm.html
---
"2012 is the year of graph databases" was the sentence that a lot of
people were hearing at the end of 2011, especially after the explosion
of Big Data associated with social networks.

At the beginning of this year, a really promising GraphDB,
[OrientDB](http://orientdb.org) , saw its first stable release (1.0.0)
which finally gave to the world a stable toy pretty different from the
traditional RDBMS that we're used to see and from the document-based DBs
like MongoDB or CouchDB: OrientDB integrates document capabilities with
a graph layer, thus it sounded very, very interesting.

Even before going stable, there were some companies already using
OrientDB, thanks to the language-specific drivers created by the
community surrounding this GraphDB: one of them, for PHP, was
[Orient](http://github.com/congow/Orient) , a bunch of classes that
wrapped PHP's native cURL functions to make queries against OrientDB's
via the HTTP protocol.

Day after day, the guys behind Orient, this PHP library, decided to add
- mostly inspired by the doctrine 1 query builder and the Doctrine2 ODMs
- abstraction and layers over that bunch of classes, finally having 3
different pieces of software to interact with OrientDB from PHP: the
HTTP binding, which does HTTP calls to the OrientDB server, the Query
Builder, which provides an object-oriented synthax to write SQL+
(OrientDB's SQL) queries and the Data Mapper, which is still unfinished.

Trying to adhere to the Doctrine2 ODMs standards, the code looks pretty
similar:

~~~~ {.sourceCode .php}
<?php

// invoking a repository
$userRepository = $manager->getRepository('My\Entity');

// creating SQL+ queries
$query = new Query();
$query->from(array('users'))->where('username = ?', "admin");

// mapping POPOs
namespace Domain;

use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Address")
*/
class Address
{
    /**
     * @ODM\Property(type="string")
     */
    public $street;
}

// finding a record
$record = $manager->find($id);
~~~~

The work done so far was interesting enough to think about an OrientDB
ODM inside the Doctrine organization, thing that eventually happened
today when the old repository has been moved to the [doctrine
organization](https://github.com/doctrine/orientdb-odm) on Github.

More news are going to come in the next weeks, as the target is to
release a stable version of the OrientDB ODM this year: you can already
[use it](https://packagist.org/packages/doctrine/orientdb-odm) or even
[fork it](https://github.com/doctrine/orientdb-odm) if you want to
contribute or propose a patch.

For further informations you can subscribe to the doctrine-dev google
group or join the IRC channel \#doctrine-dev on Freenode.
