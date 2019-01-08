---
title: "Doctrine Instantiator 1.0.0 released"
authorName: Marco Pivetta
authorEmail: ocramius@gmail.com
categories: []
permalink: /2014/09/11/instantiator-1-0-0.html
---
We released [Doctrine Instantiator
1.0.0](https://github.com/doctrine/instantiator/releases/tag/1.0.0)
several weeks ago.

This project has been migrated from
[ocramius/instantiator](https://github.com/Ocramius/Instantiator) into
the doctrine organization to have better maintenance, support as well as
handling of security related issues, which is a priority for us.

The migration has been done because all doctrine ORM and ODM projects
were affected by a quite big [backwards-incompatible change in PHP
5.4.29 and PHP 5.5.13](https://bugs.php.net/bug.php?id=67072), which
[was partially solved in PHP
5.6.0-RC3](https://github.com/php/php-src/pull/733). The main tracking
bug for this problem is \`DDC-3120\`\_.

[Doctrine Instantiator](https://github.com/doctrine/instantiator)
provides a simple API to build objects without directly relying on the
[serialization
hack](https://www.doctrine-project.org/2010/03/21/doctrine-2-give-me-my-constructor-back.html)
that has been explicitly used by all of our data mappers for quite some
time.

Installation
============

You can install [Doctrine
Instantiator](https://github.com/doctrine/instantiator) using Composer
and the following `composer.json` contents:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/instantiator": "1.0.*"
    }
}
~~~~
