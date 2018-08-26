---
title: "Doctrine Inflector release 1.1.0"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: Marco Pivetta
authorEmail: ocramius@gmail.com
categories: []
permalink: /2015/11/06/doctrine-inflector-1-1-0.html
---
We are happy to announce the immediate availability of Doctrine
Inflector
[1.1.0](https://github.com/doctrine/inflector/releases/tag/v1.1.0).

This release adds a feature that allows to upper-case words separated by
a custom delimiter
([\#11](https://github.com/doctrine/inflector/pull/11)).

We discovered that heroes, buffalo and tomatoes have something in common
([\#18](https://github.com/doctrine/inflector/pull/18)).

"criteria" and "criterion" plural and singular form were reversed: now
fixed ([\#19](https://github.com/doctrine/inflector/pull/19)).

Additional inflections were introduced for more irregular forms
([\#20](https://github.com/doctrine/inflector/pull/20)
[\#22](https://github.com/doctrine/inflector/pull/22)
[\#24](https://github.com/doctrine/inflector/pull/24)).

Last but not least, we now explicitly support and test against PHP 7
([\#21](https://github.com/doctrine/inflector/pull/21)).

Installation
============

You can install the Inflector component via the following`composer.json`
definition:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/inflector": "~1.1.0"
    }
}
~~~~
