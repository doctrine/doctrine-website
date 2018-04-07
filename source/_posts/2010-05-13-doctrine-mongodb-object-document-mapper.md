---
title: Doctrine MongoDB Object Document Mapper
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
A question asked to me many times by many different people over the last
year is, "will Doctrine ever have any support for MongoDB?". I have
never really had an answer because we haven't had any official plans to
support it as it was so new to the database world and php so nobody
really knew much about it yet.

A few weekends ago I decided to install MongoDB and give it a try. It
was pretty fun and interesting. I quickly learned that it being a
document based storage system lends itself well to a object mapper so
the experimental [Doctrine MongoDB Object Document
Mapper](http://github.com/jwage/odm) was born.

Introducing Doctrine MongoDB Object Document Mapper (ODM)
=========================================================

Much like the Doctrine 2 ORM, the ODM aims to provide transparent
persistence for PHP 5.3.0+ objects. You will notice that the Doctrine 2
ORM and ODM infrastructure, style, interfaces, etc. are all very
similar. Instead of an EntityManager like in the ORM you have the
DocumentManager. Map your objects in the same way you do for the ORM.
Here is an examle `User` document:

~~~~ {.sourceCode .php}
<?php
/** @Document(db="my_db", collection="users") */
class User
{
    /** @Id */
    private $id;

    /** @String */
    private $username;

    /** @String */
    private $password;

    // ...
}
~~~~

Now like you would expect you can create new instances and persist them
to Mongo:

~~~~ {.sourceCode .php}
<?php
$user = new User();
$user->setUsername('jwage');
$user->setPassword('testing');

$dm->persist($user);
$dm->flush();
~~~~

Now if you have a look in MongoDB you will see a database named `my_db`
and a collection named `users` containing a new document like the
following:

    array(
      '_id' => instanceof MongoId,
      'username' => 'jwage',
      'password' => 'testing'
    )

When you query for and retrieve this document from the database later,
Doctrine reconstructs the PHP object with the data from MongoDB:

~~~~ {.sourceCode .php}
<?php
$user = $dm->findOne('User', array('username' => 'jwage')); // instanceof User
echo $user->getUsername();
~~~~

Below you can find an overview list of the features available:

-   Transparent persistence.
-   Map one or many embedded documents.
-   Map one or many referenced documents.
-   Create references between documents in different databases.
-   Map documents with Annotations, XML, YAML or plain old PHP code.
-   Documents can be stored on the
    [MongoGridFS](http://www.php.net/MongoGridFS).
-   Collection per class(concrete) and single collection inheritance
    supported.
-   Map your Doctrine 2 ORM Entities to the ODM and use mixed data
    stores.
-   Inserts are performed using
    [MongoCollection::batchInsert()](http://us.php.net/manual/en/mongocollection.batchinsert.php)
-   Updates are performed using \$set instead of saving the entire
    document.

Getting Started?
================

We've put together a little documentation to help you get familiar with
the ODM quickly.

-   [Reference
    Documentation](http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/en)
-   [Getting Started Cookbook
    Article](http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/cookbook/getting-started/en)
-   [API
    Documentation](http://www.doctrine-project.org/projects/mongodb_odm/1.0/api)

How can I contribute?
=====================

Get your fork on! All you need to do is fork a Doctrine
[repository](http://github.com/doctrine) on github.com and [submit your
modifications](http://github.com/guides/fork-a-project-and-submit-your-modifications/7)
to us by sending a pull request.

You can also take part in discussions on our [mailing
list](http://groups.google.com/group/doctrine-user) or join \#doctrine
on irc.freenode.net for live support from the community.
