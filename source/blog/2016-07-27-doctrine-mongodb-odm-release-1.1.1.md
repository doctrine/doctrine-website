---
title: "Doctrine MongoDB ODM release 1.1.1
"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: Maciej Malarz
authorEmail: malarzm@gmail.com
categories: []
permalink: /2016/07/27/doctrine-mongodb-odm-release-1.1.1.html
---
We are happy to announce the immediate availability of Doctrine MongoDB
ODM
[1.1.1](https://github.com/doctrine/mongodb-odm/releases/tag/1.1.1).

Bug fixes in this release
=========================

Notable fixes may be found in the
[changelog](https://github.com/doctrine/mongodb-odm/blob/master/CHANGELOG-1.1.md#111-2016-07-27).
A full list of issues and pull requests included in this release may be
found in the [1.1.1
milestone](https://github.com/doctrine/mongodb-odm/issues?q=milestone%3A1.1.1).

Installation
============

You can install the latest version using the following `composer.json`
definitions:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/mongodb-odm": "^1.1.1"
    }
}
~~~~

Future Releases
===============

Work on the much anticipated 2.0 version of MongoDB ODM with support for
the new MongoDB driver is beginning; however we are unable to set a
release date yet. Development will likely take some time and thus we
have scheduled a 1.2 version to be released before 2.0. Version 1.2 will
include all features planned for 2.0 that can be introduced in a
backward compatible way as well as new deprecation notices for
functionality we plan to remove in 2.0, which we hope will ease future
migration.

Doctrine MongoDB ODM release 1.0.7
==================================

We are also happy to announce the immediate availability of Doctrine
MongoDB ODM
[1.0.7](https://github.com/doctrine/mongodb-odm/releases/tag/1.0.7).

Notable fixes may be found in the
[changelog](https://github.com/doctrine/mongodb-odm/blob/master/CHANGELOG-1.0.md#107-2016-07-27).
A full list of issues and pull requests included in this release may be
found in the [1.0.7
milestone](https://github.com/doctrine/mongodb-odm/issues?q=milestone%3A1.0.7).

You can install the latest version using the following `composer.json`
definitions:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/mongodb-odm": "^1.0.7"
    }
}
~~~~
