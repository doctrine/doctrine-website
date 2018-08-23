---
title: "Doctrine MongoDB ODM 1.0.0ALPHA2 Released"
menuSlug: blog
layout: blog-post
authorName: jwage
authorEmail:
categories: [release]
permalink: /2010/07/30/doctrine-mongodb-odm-1-0-0alpha2-released.html
---
Today I am happy to bring you the second alpha release of the brand new
Doctrine MongoDB Object Document Mapper (ODM). The release contains
several bug fixes and a few enhancements. You now have the ability to
mix types of documents in references and embedded documents. You can
read the [blog
post](http://www.doctrine-project.org/blog/mixing-types-of-documents)
about mixing types of documents to learn more.

<h2>

Bug

</h2>
<ul>
<li>

[MODM-12] - Undefined variable error

</li>
<li>

[MODM-13] - Problems with merge()

</li>
<li>

[MODM-14] - Default database when @Document annotation does not contain
a db attribute.

</li>
<li>

[MODM-16] - loadById failing on custom ids

</li>
<li>

[MODM-17] - Multiple array / object with same content/properties do not
get persisted

</li>
<li>

[MODM-19] - Field annotation missing "name" support

</li>
<li>

[MODM-20] - SINGLE\_COLLECTION inheritance does not work

</li>
<li>

[MODM-21] - AnnotationDriver doesn't allow custom types

</li>
<li>

[MODM-22] - Document incorrectly scheduled for update

</li>
<li>

[MODM-25] - AnnotationDriver.php Line 175

</li>
<li>

[MODM-28] - xml mapping : embedded-document node is ignored, can't
persist

</li>
</ul>

<h2>

Improvement

</h2>
<ul>
<li>

[MODM-24] - Hydratation of extra fields

</li>
<li>

[MODM-26] - MongoCursor doesn't implement Countable interface

</li>
</ul>

Download
========

You can directly download the PEAR package file
[here](http://www.doctrine-project.org/downloads/DoctrineMongoDBODM-1.0.0ALPHA2.tgz).
You can manually extract the code or you can install the PEAR package
file locally.

    $ pear install /path/to/DoctrineMongoDBODM-1.0.0ALPHA2.tgz

Or you can checkout from github:

    $ git clone git://github.com/doctrine/mongodb-odm.git mongodb_odm
    $ cd mongodb_odm
    $ git checkout 1.0.0ALPHA2

And you can also install via PEAR:

    $ pear install pear.doctrine-project.org/DoctrineMongoDBODM-1.0.0ALPHA2
