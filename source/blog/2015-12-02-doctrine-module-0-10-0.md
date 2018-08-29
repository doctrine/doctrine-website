---
title: "DoctrineModule 0.10.0 Release"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: Gianluca Arbezzano
authorEmail: gianarb92@gmail.com
categories: []
permalink: /2015/12/02/doctrine-module-0-10-0.html
---
We are happy to announce DoctrineModule `0.10.0`.

Installation
============

You can install this version of the DoctrineModule by using Composer and
the:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/doctrine-module": "~0.10.0"
    }
}
~~~~

Changes since 0.9.0
===================

This is a list of issues resolved in `2.5.2` since `2.4.1`:

-   [[\#521]](https://github.com/doctrine/DoctrineModule/pull/521):
    Fixed php\_codesniffer dependency
-   [[\#534]](https://github.com/doctrine/DoctrineModule/pull/534):
    Fixed wrong Predis Mock
-   [[\#537]](https://github.com/doctrine/DoctrineModule/pull/537):
    Update hydrator.md
-   [[\#520]](https://github.com/doctrine/DoctrineModule/pull/520): Fix
    for issue \#230 and fixes for \#234
-   [[\#539]](https://github.com/doctrine/DoctrineModule/pull/539):
    Better support for snake\_case field names
-   [[\#535]](https://github.com/doctrine/DoctrineModule/pull/535): Adds
    additional processing for DoctrineObject::toMany

Please report any issues you may have with the update on the mailing
list or on
[GitHub](https://github.com/doctrine/DoctrineModule/issues).
