---
title: "MongoDB ODM 1.0.0BETA1 Released"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: jwage
authorEmail:
categories: [release]
permalink: /2010/08/18/mongodb-odm-1-0-0beta1-released.html
---
Today I am happy to tell you we have released the first beta version of
the MongoDB Object Document Mapper. It contains many fixes and general
improvements across the code.

We fixed lots of things reported by our users in Jira but also made lots
of other improvements like improving the use of atomic operators. Read
on for a complete list of the issues fixed in Jira:

Fixed Jira Issues
=================

<ul>
<li>

[MODM-32] - dbref \$id persisted as string instead of objectid

</li>
<li>

[MODM-33] - Class-level annotations are ignored if set on
MappedSuperclass

</li>
<li>

[MODM-34] - Custom Id always gets sent with changeset

</li>
<li>

[MODM-35] - Proxy item gets reset on persistent collection load if that
item was in the collection

</li>
<li>

[MODM-36] - Embedded relations are not persisted after a flush()

</li>
<li>

[MODM-37] - Problems with EmbedMany and discrimatorMap and
discriminatorField

</li>
<li>

[MODM-38] - Using YAML description with embedMany causes PHP notice
error

</li>
<li>

[MODM-41] - Hydration down not work for annotation "@ReferenceMany"

</li>
<li>

[MODM-42] - PersistentCollection fails when working with MongoGridFs

</li>
<li>

[MODM-45] - Doctrine doesn't persist empty objects

</li>
<li>

[MODM-46] - @AlsoLoad annotation causes exception when used together
with Embed/Reference annotations

</li>
<li>

[MODM-47] - @AlsoLoad annotation, used on method causes fatal error

</li>
<li>

[MODM-48] - Embedded document changes are ignored if it was empty before

</li>
<li>

[MODM-49] - Getting PHP notice and warning with empty persistent
collection

</li>
<li>

[MODM-50] - GridFs file classes don't support inheritance

</li>
<li>

[MODM-43] - Explicit schema migration

</li>
<li>

[MODM-40] - Move value scalarization and comparison to Unit Of Work

</li>
</ul>

Download
========

You can directly download the PEAR package file
[here](http://www.doctrine-project.org/downloads/DoctrineMongoDBODM-1.0.0BETA1.tgz).
You can manually extract the code or you can install the PEAR package
file locally.

    $ pear install /path/to/DoctrineMongoDBODM-1.0.0BETA1.tgz

Checkout from github
--------------------

    $ git clone git://github.com/doctrine/mongodb-odm.git mongodb_odm
    $ cd mongodb_odm
    $ git checkout 1.0.0BETA1

Install via PEAR
----------------

    $ pear install pear.doctrine-project.org/DoctrineMongoDBODM-1.0.0BETA1
