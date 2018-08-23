---
title: "Doctrine 2.4 released"
menuSlug: blog
layout: blog-post
authorName: Benjamin Eberlei
authorEmail:
categories: [release]
permalink: /2013/09/11/doctrine-2-4-released.html
---
We are happy to announce the availability of Doctrine Common, DBAL and
ORM versions 2.4. This took us much longer than planned and we are very
sorry for all the delay that was mostly caused by contributers private
and work lifes. For the next versions we will also try to keep the scope
smaller, you will see that version 2.4 contains lots of small new
features.

Starting with version 2.4 Doctrine will not be available over PEAR
anymore. The maintenance of this deployment channel is too complicated,
compared to the small number of people using it. We focus on shipping
Doctrine with [Composer](http://getcomposer.org) , which is a superior
packaging tool in our opinion. We will continue to make Doctrine
available as standalone download through the Github releases pages.

Follow the links in the list to find the changelogs of the three new
releases:

-   [Doctrine Common v2.4.1
    Changelog](https://github.com/doctrine/common/releases/tag/v2.4.1)
-   [Doctrine DBAL v2.4.0
    Changelog](https://github.com/doctrine/dbal/releases/tag/v2.4.0)
-   [Doctrine ORM v2.4.0
    Changelog](https://github.com/doctrine/doctrine2/releases/tag/v2.4.0)

Backwards Incompatible Changes
==============================

There have been some BC breaks in the 2.4 releases, which are listed
here:

-   DoctrineDBALSchemaConstraint API change
-   Compatibility Bugfix in PersistentCollection\#matching()
-   Composer is now the default autoloader
-   OnFlush and PostFlush event always called
-   DQL: Parenthesis are now considered in arithmetic expression

You can read up in detail on BC breaks on the
[DBAL](https://github.com/doctrine/dbal/blob/2.4/UPGRADE) and
[ORM](https://github.com/doctrine/doctrine2/blob/2.4/UPGRADE.md) UPGRADE
docs.

Installation
============

You can install Doctrine using Composer and the following
`composer.json` contents:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/common": "2.4.*",
        "doctrine/dbal": "2.4.*",
        "doctrine/orm": "2.4.*"
    }
}
~~~~

New Features
============

This release contains large amount of new features and improvements.
Compared to the previous minor release not so many big-bang features,
but many small improvements. The most important changes are listed here
with small examples or links to their documentation.

-   ALTER TABLE support for SQLite (by
    [hason](https://github.com/hason)) by creating new tables, moving
    all the data and then renaming.
-   Using \`EXTRA\_LAZY\` fetch mode now also queries for single
    entities on collections with `indexBy` when using
    `$collection->get()` or accessing the collection via array access.
-   Added two new modes to proxy generation for development
    environemnts, first using `eval` and second by checking if the proxy
    file not exists.
-   Pass Event Arguments to entities lifecycle methods, allowing access
    to `EntityManager`.
-   Allow to order by associations when using
    `EntityRepository#findBy()`.
-   Support for new \`NEW()\` operator in DQL which can be wrapped
    around the full `SELECT`-clause parts and instantiates an object by
    passing the parameters to the constructor. See the
    [documentation](http://docs.doctrine-project.org/en/latest/reference/dql-doctrine-query-language.html#new-operator-syntax)
    for more details.
-   Support for `@EntityListener` annotation and XML/YML configuration,
    which allows adding listener services on a per entity level (not
    global). See the
    [documentation](http://docs.doctrine-project.org/en/latest/reference/events.html#entity-listeners)
    for details.
-   Improved the `ResultSetMapping` to generate the `SELECT` clause for
    an SQL statement, thereby improving the usability of native queries
    alot. See the
    [documentation](http://docs.doctrine-project.org/en/latest/reference/native-sql.html#resultsetmappingbuilder)
    for more details.
-   Introduced a factory for repositories that can be overwritten in the
    `Doctrine\ORM\Configuration`.
-   Added an interface for `EntityManager` called
    `Doctrine\ORM\EntityManagerInterface` and a decorator base class
    `Doctrine\ORM\Decorator\EntityManagerDecorator`.
-   Support for proxy objects for entities with public properties.
-   Add support for composite primary keys in DQL `IDENTITY()` function.
    See the
    [documentation](http://docs.doctrine-project.org/en/latest/reference/dql-doctrine-query-language.html#dql-select-examples)
    for more details.
-   Introduced ANSI SQL Quoting Strategy that does not attempt to quote
    and modify columns during SQL generation.
-   Joins between arbitrary entities are now possible in DQL by using
    the syntax `FROM Foo f JOIN Bar b WITH f.id = b.id`.

Documentation
=============

With 2.4 we merged the documentation into the repositories itself. We
have seen a much increased amount of Pull Requests since then and also
improved the documentation ourselves quite alot for 2.4.

Besides fixes and additions of all the new features, we tried to improve
the style of the documentation. Many of the chapters have been
refactored completly and hopefully address many of the valid concers of
our users.
