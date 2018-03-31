---
title: Doctrine Mongo ODM Module release 0.8.2
menuSlug: blog
authorName: Gianluca Arbezzano
authorEmail: gianarb92@gmail.com
categories: []
indexed: false
---
The **Zend Framework Integration Team** is happy to announce the new release of **DoctrineMongoODMModule**.  
DoctrineMongoODMModule ``0.8.2`` will be the last bugfix version with support for **DoctrineModule** ``0.8``, and in consequence, it is the last version that will support PHP 5.3.
Further versions of the ``0.8.*`` series may still be released in case of security issues.

Following issues were solved in this release:

- `108 - applying correct chmod() to generated cache file <https://github.com/doctrine/DoctrineMongoODMModule/pull/108>`_
- `109 - added user guide <https://github.com/doctrine/DoctrineMongoODMModule/pull/109>`_
- `114 - simplified unit testing <https://github.com/doctrine/DoctrineMongoODMModule/pull/114>`_
- `115 - removed files for old test setup <https://github.com/doctrine/DoctrineMongoODMModule/pull/115>`_

To install this version, simply update your `composer.json`: 

.. code-block:: json

  {
      "require": {
          "doctrine/doctrine-mongo-odm-module": "0.8.2"
      }
  }
