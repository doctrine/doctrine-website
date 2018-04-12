---
title: Doctrine MongoDB ODM release 1.0.0-BETA13
menuSlug: blog
authorName: Maciej Malarz
authorEmail: malarzm@gmail.com
categories: []
permalink: /2015/05/22/doctrine-mongodb-odm-release-1-0-0-beta13.html
---
We are happy to announce the immediate availability of Doctrine MongoDB
ODM 1.0.0-BETA13.

What is new in 1.0.0-BETA13?
============================

All issues and pull requests in this release may be found under the
[1.0.0-BETA13 milestone on
github](https://github.com/doctrine/mongodb-odm/issues?q=milestone%3A1.0.0-BETA13).
Here is the highlight of most important features:

atomicSet and atomicSetArray strategies for top-level collections
-----------------------------------------------------------------

[\#1096](https://github.com/doctrine/mongodb-odm/pull/1096) introduces
two new collection update strategies, `atomicSet` and `atomicSetArray`.
Unlike existing strategies (e.g. `pushAll` and `set`), which update
collections in a separate query after the parent document, the atomic
strategy ensures that the collection and its parent are updated in the
same query. Any nested collections (within embedded documents) will also
be included in the atomic update, irrespective of their update
strategies.

Currently, atomic strategies may only be specified for collections
mapped directly in a document class (i.e. not collections within
embedded documents). This strategy may be especially useful for highly
concurrent applications and/or versioned document classes (see:
[\#1094](https://github.com/doctrine/mongodb-odm/pull/1094)).

Reference priming improvements
------------------------------

[\#1068](https://github.com/doctrine/mongodb-odm/pull/1068) moves the
handling of primed references to the Cursor object, which allows ODM to
take the skip and limit options into account and avoid priming more
references than are necessary.

[\#970](https://github.com/doctrine/mongodb-odm/pull/970) now allows
references within embedded documents to be primed by fixing a previous
parsing limitation with dot syntax in field names.

New defaultDiscriminatorValue mapping
-------------------------------------

[\#1072](https://github.com/doctrine/mongodb-odm/pull/1072) introduces
a `defaultDiscriminatorValue` mapping, which may be used to specify a
default discriminator value if a document or association has no
discriminator set.

New Integer and Bool annotation aliases
---------------------------------------

[\#1073](https://github.com/doctrine/mongodb-odm/pull/1073) introduces
`Integer` and `Bool` annotations, which are aliases of `Int` and
`Boolean`, respectively.

Add millisecond precision to DateType
-------------------------------------

[\#1063](https://github.com/doctrine/mongodb-odm/pull/1063) adds
millisecond precision to ODM's DateType class (note: although PHP
supports microsecond precision, dates in MongoDB are limited to
millisecond precision). This should now allow ODM to roundtrip dates
from the database without a loss of precision.

New Hydrator generation modes
-----------------------------

Previously, the `autoGenerateHydratorClasses` ODM configuration option
was a boolean denoting whether to always or never create Hydrator
classes. As of
[\#953](https://github.com/doctrine/mongodb-odm/pull/953), this option
now supports four modes:

-   `AUTOGENERATE_NEVER = 0` (same as `false`)
-   `AUTOGENERATE_ALWAYS = 1` (same as `true`)
-   `AUTOGENERATE_FILE_NOT_EXISTS = 2`
-   `AUTOGENERATE_EVAL = 3`

Support for custom DocumentRepository factory
---------------------------------------------

[\#892](https://github.com/doctrine/mongodb-odm/pull/892) allows users
to define a custom repository class via the `defaultRepositoryClassName`
configuration option. Alternatively, a custom factory class may also be
configured, which allows users complete control over how repository
classes are instantiated.

Custom repository and factory classes must implement
`Doctrine\Common\Persistence\ObjectRepository` and
`Doctrine\ODM\MongoDB\Repository\RepositoryFactory`, respectively.

Stay tuned, there is a lot more to come soon!
---------------------------------------------
