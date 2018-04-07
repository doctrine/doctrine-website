---
title: Doctrine MongoDB ODM 1.0.0ALPHA2 Released
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: [release]
permalink: /:year/:month/:day/:basename.html
---
Today I am happy to bring you the second alpha release of the brand
new Doctrine MongoDB Object Document Mapper (ODM). The release
contains several bug fixes and a few enhancements. You now have the
ability to mix types of documents in references and embedded
documents. You can read the
`blog post <http://www.doctrine-project.org/blog/mixing-types-of-documents>`_
about mixing types of documents to learn more.


.. raw:: html

   <h2>        
   
Bug

.. raw:: html

   </h2>
   <ul>
   <li>
   
[MODM-12] - Undefined variable error

.. raw:: html

   </li>
   <li>
   
[MODM-13] - Problems with merge()

.. raw:: html

   </li>
   <li>
   
[MODM-14] - Default database when @Document annotation does not
contain a db attribute.

.. raw:: html

   </li>
   <li>
   
[MODM-16] - loadById failing on custom ids

.. raw:: html

   </li>
   <li>
   
[MODM-17] - Multiple array / object with same content/properties do
not get persisted

.. raw:: html

   </li>
   <li>
   
[MODM-19] - Field annotation missing "name" support

.. raw:: html

   </li>
   <li>
   
[MODM-20] - SINGLE\_COLLECTION inheritance does not work

.. raw:: html

   </li>
   <li>
   
[MODM-21] - AnnotationDriver doesn't allow custom types

.. raw:: html

   </li>
   <li>
   
[MODM-22] - Document incorrectly scheduled for update

.. raw:: html

   </li>
   <li>
   
[MODM-25] - AnnotationDriver.php Line 175

.. raw:: html

   </li>
   <li>
   
[MODM-28] - xml mapping : embedded-document node is ignored, can't
persist

.. raw:: html

   </li>
   </ul>
   
   <h2>        
   
Improvement

.. raw:: html

   </h2>
   <ul>
   <li>
   
[MODM-24] - Hydratation of extra fields

.. raw:: html

   </li>
   <li>
   
[MODM-26] - MongoCursor doesn't implement Countable interface

.. raw:: html

   </li>
   </ul>
   
Download
--------

You can directly download the PEAR package file
`here <http://www.doctrine-project.org/downloads/DoctrineMongoDBODM-1.0.0ALPHA2.tgz>`_.
You can manually extract the code or you can install the PEAR
package file locally.

::

    $ pear install /path/to/DoctrineMongoDBODM-1.0.0ALPHA2.tgz

Or you can checkout from github:

::

    $ git clone git://github.com/doctrine/mongodb-odm.git mongodb_odm
    $ cd mongodb_odm
    $ git checkout 1.0.0ALPHA2

And you can also install via PEAR:

::

    $ pear install pear.doctrine-project.org/DoctrineMongoDBODM-1.0.0ALPHA2
