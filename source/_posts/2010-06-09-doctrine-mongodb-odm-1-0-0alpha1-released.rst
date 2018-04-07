---
title: Doctrine MongoDB ODM 1.0.0ALPHA1 Released
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: [release]
permalink: /:year/:month/:day/:basename.html
---
Today I am very happy to announce the release of the first alpha
version of the Doctrine `MongoDB <http://www.mongodb.org>`_ Object
Document Mapper. This is exciting as it is the beginning of a whole
new chapter in the life of the Doctrine Project. We hope to
continue adding packages to allow you to transparently persist your
domain objects to a variety of different database engines!

Features
--------

Below you will find a list of some of the features provided by the
Doctrine MongoDB Object Document Mapper:


-  Transparent persistence.
-  Map one or many
   `embedded <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/embedded-mapping/en>`_
   documents.
-  Map one or many
   `referenced <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/reference-mapping/en>`_
   documents.
-  Create references between documents in different databases.
-  Map documents with
   `Annotations <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/annotations-reference/en>`_ ,
   `XML <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/xml-mapping/en#xml-mapping>`_ ,
   `YAML <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/yml-mapping/en#yml-mapping>`_
   or plain old PHP code.
-  Documents can be stored on the
   `MongoGridFS <http://www.php.net/MongoGridFS>`_.
-  Collection per class(concrete) and single collection
   `inheritance <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/inheritance-mapping/en>`_
   supported.
-  Map your Doctrine 2 ORM Entities to the ODM and use mixed data
   stores.
-  Inserts are performed using
   `MongoCollection::batchInsert() <http://us.php.net/manual/en/mongocollection.batchinsert.php>`_
-  Updates are performed using the atomic operators $set, $pullAll,
   $pushAll and $increment instead of saving the entire document.
-  `Migrate <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/migrating-schemas/en>`_
   your schema as your domain model evolves and changes.
-  `Fluent Object Oriented Interface <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/query-api/en>`_
   for building and executing queries.
-  `Map Reduce <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/map-reduce/en>`_
   integration.

You can continue reading the
`Introduction <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/introduction/en>`_
chapter in the reference documentation to get a grasp of what
exactly the Doctrine MongoDB Object Document Mapper does by looking
at some examples!

Documentation
-------------

Want documentation? You got it! Check out the links below to get
started learning about the Doctrine MongoDB Object Document
Mapper:


-  `Reference Documentation <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/reference/en>`_
-  `Getting Started Cookbook Article <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/cookbook/getting-started/en>`_
-  `API Documentation <http://www.doctrine-project.org/projects/mongodb_odm/1.0/api>`_

We'll be adding more
`Cookbook <http://www.doctrine-project.org/projects/mongodb_odm/1.0/docs/cookbook>`_
articles in the coming weeks so check back for more documentation!

Download
--------

You can see all the download information for the Doctrine MongoDB
ODM on the projects
`download <http://www.doctrine-project.org/projects/mongodb_odm/download>`_
page. You have several ways to get the code which are described
below as well:

Checkout from Git
~~~~~~~~~~~~~~~~~

::

    $ git clone git://github.com/doctrine/mongodb-odm.git mongodb_odm
    $ cd mongodb_odm
    $ git checkout 1.0.0ALPHA1

Download PEAR Package
~~~~~~~~~~~~~~~~~~~~~

You can manually download the PEAR package
`here <http://www.doctrine-project.org/downloads/DoctrineMongoDBODM-1.0.0ALPHA1.tgz>`_.
If you want you can manually unarchive the code or you can
``pear install`` the downloaded package:

::

    $ pear install /path/to/downloads/DoctrineMongoDBODM-1.0.0ALPHA1.tgz

Install from PEAR
~~~~~~~~~~~~~~~~~

You can also directly install using our
`PEAR Server <http://pear.doctrine-project.org>`_:

::

    $ pear install pear.doctrine-project.org/DoctrineMongoDBODM-1.0.0ALPHA1

Reporting Issues
----------------

If you encounter any problems with the Doctrine MongoDB Object
Document Mapper you can report new issues to the
`Jira <http://www.doctrine-project.org/jira/browse/MODM>`_ project.
For more information about contributing to Doctrine you can
checkout the documentation for our
`Contributor Workflow <http://www.doctrine-project.org/contribute>`_.
