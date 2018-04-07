---
title: Doctrine MongoDB ODM release 1.0.5
menuSlug: blog
authorName: Maciej Malarz
authorEmail: malarzm@gmail.com
categories: []
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate availability of Doctrine MongoDB
ODM
[1.0.5](https://github.com/doctrine/mongodb-odm/releases/tag/1.0.5).

Bug fixes in this release
=========================

Notable fixes may be found in the
[changelog](https://github.com/doctrine/mongodb-odm/blob/master/CHANGELOG-1.0.md#105-2016-02-16).
A full list of issues and pull requests included in this release may be
found in the [1.0.5
milestone](https://github.com/doctrine/mongodb-odm/issues?q=milestone%3A1.0.5).

Installation
============

You can install the latest version using the following `composer.json`
definitions:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/mongodb-odm": "^1.0.5"
    }
}
~~~~

Doctrine MongoDB ODM and PHP 7
==============================

While ODM still relies on legacy MongoDB driver ([i.e.
ext-mongo](https://pecl.php.net/package/mongo)) and no dates are
scheduled for the 2.0 release, it is possible to run ODM's development
branch with the new MongoDB driver (i.e.
[ext-mongodb](http://php.net/manual/en/mongodb.installation.php)) on
PHP 7 and HHVM! [(see: this
tweet)](https://twitter.com/alcaeus/status/697659616172359680) The new
driver should be properly supported once we release versions 1.1 and 1.3
of the ODM and underlying [Doctrine
MongoDB](https://github.com/doctrine/mongodb) library, respectively.
This is all possible thanks to our Andreas Braun's
([@alcaeus](https://twitter.com/alcaeus)) work on
[mongo-php-adapter](https://github.com/alcaeus/mongo-php-adapter)
which implements `ext-mongo's` API atop `ext-mongodb` and
[mongodb-php-library](https://github.com/mongodb/mongo-php-library)."
If you can't wait to give ODM a test flight on PHP 7, now is the time!
Also, if you happen to meet Andreas be sure to buy him a beer :)
