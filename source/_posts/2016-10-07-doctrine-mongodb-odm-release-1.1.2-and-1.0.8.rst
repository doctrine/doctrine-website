---
title: Doctrine MongoDB ODM 1.1.2 and 1.0.8 released
menuSlug: blog
authorName: Andreas Braun
authorEmail: alcaeus@alcaeus.org
categories: []
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate availability of Doctrine MongoDB ODM
`1.1.2 <https://github.com/doctrine/mongodb-odm/releases/tag/1.1.2>`__ and
`1.0.8 <https://github.com/doctrine/mongodb-odm/releases/tag/1.0.8>`__.

MongoDB ODM 1.0.8
-----------------

- calling ``dropCollections()`` in the SchemaManager did not drop GridFS
collections. `#1468 <https://github.com/doctrine/mongodb-odm/pull/1468>`_
- calling ``clear()`` on an uninitialized collection with ``orphanRemoval``
enabled failed to remove orphaned documents. `#1500 <https://github.com/doctrine/mongodb-odm/pull/1500>`_
- Documents with identifiers evaluating to ``false`` (e.g. empty string or 0)
could not be reference using ``createDBRef()`` in DocumentManager. `#1503 <https://github.com/doctrine/mongodb-odm/pull/1503>`_

MongoDB ODM 1.1.2
-----------------

- This release contains the bugfixes outlined for ODM 1.0.8 above
- Querying for referenced objects in ``findBy()`` or ``findOneBy()`` did not work
properly due to incorrect preparation of the DBRef objects. `#1481 <https://github.com/doctrine/mongodb-odm/pull/1481>`_

Installation
------------

You can install the latest version using the following ``composer.json`` definitions:

.. code-block:: json

  {
      "require": {
          "doctrine/mongodb-odm": "^1.1.2"
      }
  }

Support for Doctrine ODM 1.0.x
------------------------------

As outlined previously, support for MongoDB ODM 1.0.x will end on December 9th,
2016. If you have not upgraded to version 1.1 yet, please do so to receive
future bug fixes.
