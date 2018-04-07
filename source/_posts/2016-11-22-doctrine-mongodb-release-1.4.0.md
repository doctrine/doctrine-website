---
title: Doctrine MongoDB 1.4.0
menuSlug: blog
authorName: Andreas Braun
authorEmail: alcaeus@alcaeus.org
categories: []
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate availability of Doctrine MongoDB
Abstraction Layer
[1.4.0](https://github.com/doctrine/mongodb/releases/tag/1.4.0).

Passing context options to the driver
=====================================

With this release it's possible to pass driver options to the connection
class, which will then be passed on to the MongoDB driver. For example,
to pass a stream context with SSL context options, you could use the
following code snippet:

~~~~ {.sourceCode .php}
$context = stream_context_create([
    'ssl' => [
        'allow_self_signed' => false,
    ]
]);
$connection = new \Doctrine\MongoDB\Connection(null, [], null, null, ['context' => $context]);
~~~~

Passing multiple expressions to logical operators
=================================================

The `addAnd`, `addNor` and `addOr` methods in the query and aggregation
builders now take multiple expression objects. Instead of having to call
the method repeatedly, you may call it once with multiple arguments:

~~~~ {.sourceCode .php}
// Before
$builder
    ->addAnd($someExpression)
    ->addAnd($otherExpression);

// After
$builder->addAnd($someExpression, $otherExpression);
~~~~

Deprecations
============

The `update` and `multiple` methods in the query have been deprecated in
favor of `updateOne` and `updateMany`. These deprecations help people
using ODM prepare for the next version of ODM which will utilize the new
MongoDB library API.

Bug fixes in this release
=========================

Notable fixes may be found in the
[changelog](https://github.com/doctrine/mongodb/blob/master/CHANGELOG-1.4.md#140-2016-11-22).
A full list of issues and pull requests included in this release may be
found in the [1.4.0
milestone](https://github.com/doctrine/mongodb/issues?q=milestone%3A1.4.0).

PHP version support
===================

With this release, we have dropped support for PHP 5.5. Users using PHP
5.5 or older are encouraged to upgrade to a newer PHP version. If you
are using PHP 7.0 or 7.1, you can use this library by adding a polyfill
for `ext-mongo`, like
[mongo-php-adapter](https://github.com/alcaeus/mongo-php-adapter).

Future releases
===============

This release is the last planned minor release of the MongoDB
Abstraction Layer, with only bugfixes being done in maintenance
releases. The library will not be rewritten to support the new MongoDB
driver. Users are encouraged to use the new [MongoDB
library](https://github.com/mongodb/mongo-php-library). Doctrine
MongoDB ODM will be adapted to support the new driver and the MongoDB
library.

Installation
============

You can install the latest version using the following `composer.json`
definitions:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/mongodb": "^1.4.0"
    }
}
~~~~
