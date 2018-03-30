---
title: Doctrine Instantiator 1.0.0 released
authorName: Marco Pivetta
authorEmail: ocramius@gmail.com
categories: []
indexed: false
---
We released `Doctrine Instantiator 1.0.0`_ several weeks ago.

This project has been migrated from `ocramius/instantiator`_ into the doctrine organization to
have better maintenance, support as well as handling of security related issues, which is a
priority for us.

The migration has been done because all doctrine ORM and ODM projects were affected by a
quite big `backwards-incompatible change in PHP 5.4.29 and PHP 5.5.13`_, which
`was partially solved in PHP 5.6.0-RC3`_. The main tracking bug for this problem is `DDC-3120`_.

`Doctrine Instantiator`_ provides a simple API to build objects without directly relying on
the `serialization hack`_ that has been explicitly used by all of our data mappers for quite
some time.

Installation
------------

You can install `Doctrine Instantiator`_ using Composer and the following ``composer.json``
contents:

.. code-block:: json

    {
        "require": {
            "doctrine/instantiator": "1.0.*"
        }
    }

.. _Doctrine Instantiator 1.0.0: https://github.com/doctrine/instantiator/releases/tag/1.0.0
.. _Doctrine Instantiator: https://github.com/doctrine/instantiator
.. _ocramius/instantiator: https://github.com/Ocramius/Instantiator
.. _backwards-incompatible change in PHP 5.4.29 and PHP 5.5.13: https://bugs.php.net/bug.php?id=67072
.. _was partially solved in PHP 5.6.0-RC3: https://github.com/php/php-src/pull/733
.. _DDC-3120: http://www.doctrine-project.org/jira/browse/DDC-3120
.. _serialization hack: http://www.doctrine-project.org/2010/03/21/doctrine-2-give-me-my-constructor-back.html
