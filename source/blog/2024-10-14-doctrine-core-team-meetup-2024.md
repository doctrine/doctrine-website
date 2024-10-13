---
title: "Doctrine Core Team Meetup, ORM 2.19.8, 2.20.0, 3.3.0 Releases, DBAL 4.2.0"
authorName: Benjamin Eberlei
authorEmail: kontakt@beberlei.de
permalink: /2024/10/14/doctrine-core-team-meetup-2024.html
---

Last week, we meet with the Doctrine ORM and MongoDB Core Teams for 3 days in
Bonn, Germany. A few releases followed immediately from that, including some
deprecation reversals and a discussion of our upcoming roadmap.

* [Doctrine ORM 3.3.0](https://github.com/doctrine/orm/releases/tag/3.3.0)
* [Doctrine ORM 2.20.0](https://github.com/doctrine/orm/releases/tag/2.20.0)
* [Doctrine ORM 2.19.8](https://github.com/doctrine/orm/releases/tag/2.19.8)
* [Doctrine DBAL 4.2.0](https://github.com/doctrine/dbal/releases/tag/4.2.0)
* [Doctrine Migrations 3.8.2](https://github.com/doctrine/migrations/releases/tag/3.8.2)

We want to thank all our sponsors on
[OpenCollective](https://opencollective.com/doctrine) and
[GitHub](https://github.com/sponsors/doctrine) for contributing towards making
this in-person team meetup possible. If you are not a sponsor of Doctrine already,
[please consider becoming one](https://www.doctrine-project.org/sponsorship.html).

## Undeprecation of PARTIAL Object Hydration

Starting with ORM 3.3.0 you can use `SELECT PARTIAL` DQL syntax again and with
ORM 2.20 you will not get a deprecation message for using that anymore. If your
application uses partial objects with DQL, you can migrate from 2.20 to 3.3.0
now without having to change their use. Partial objects have exactly the same
behavior (and downsides) in version 3 and 2.

Why the change? It was a long time plan to remove partial objects and their
hydration, because of the many edge cases they produced. This plan was in
effect and shipped for 3.0 - 3.2, where the syntax and feature was completely
removed. In discussion with our users we found a lot of use-cases and ideas
that are powerful with partial objects, but were firm on our goal to remove
partial objects. 

This all changes with PHP 8.4 and its lazy object feature. With this feature we
will be able to implement partial objects transparently to PHP. That means it
will not be necessary to know if an object is a proxy, a partial object, or a
full object. Whenever a property that is not available is accessed, Doctrine
can load it.

We hope to add this behavior to ORM 3.4 in the next months.

## Support for PHP 8.4 and Property Hooks

Doctrine supports PHP 8.4 starting with ORM 2.20.0 and 3.3.0, DBAL 4 and 3.

There are caveats though. You cannot use property hooks in entities yet. This
is because we need to rework internally how we read and write property values
(`ReflectionProperty::setValue()` vs `setRawValue()` and `ReflectionProperty::getValue()`
and `getRawValue()`). For now, if you try to create a property hook on an entity,
Doctrine will throw an exception. We plan to address this with an upcoming ORM
3.4 release, hopefully before PHP 8.4 is released itself, otherwise shortly
after.

For MongoDB ODM, PHP 8.4 support depends on a to-be-completed migration from
ProxyManager to `symfony/var-exporter`. 

## Lazy Objects and PHP 8.4 requirement in ORM 4

The lazy objects RFC in PHP 8.4 changes everything for the better in Doctrine
ORM internally. This is why we decided that ORM 4 will be mainly a decision
about exclusively using lazy objects and therefore will require PHP 8.4.

## Support for ENUM Database types

DBAL 4.2 now supports a new Enum type that is mainly useful for introspection
of database schemas that contain enums. All values of the enum are parsed out
of the type and are available to the schema abstraction layer.

Although we do not recommend the enum type in MySQL/MariaDB due to its quirky
implementation details, you can also use this type to directly map columns to
enums in the database via the ORM as of 3.3.0:

```
class Subscription
{
    #[Column(type: "enum", options: ['values' => ['future', 'active, 'cancelled', 'expired']))]
    public string $state = 'future';
}
```

You can also map Enums directly to MySQL enums and let Doctrine auto-detect al lthe configuration:

```
class Subscription
{
    #[Column(type: Types::ENUM)]
    public State $state = State::FUTURE;
}
enum State : string
{
    case FUTURE = 'future';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
```

We have also discussed at length how we can make types parameterized, which
will further improve Doctrine Schema introspection and comparisons with
non-default types.

## Query Cache and Pagination Variables

If you have used `setFirstResult()` and `setMaxResults()` with DQL queries then up
until 2.20.x of Doctrine, each combination of first result and max results lead
to their own DQL quer parsing cache entry. This could easily balloon the query
cache size out of control.

Starting with 2.20, the DQL parser is now running in a two-step process, where
the first step generates the cacheable result and the second step amends the
cached result with the LIMIT query part (or other database equivalent).

This is a bigger internal change, and we hope that we thought of all the edge
cases, but it might be possible that especially in combination with the
Paginator abstraction and collection fetch joins, there are some cases
where this change could lead to breaks on upgrading to 2.20 from 2.19.

## DQL: Nested DTOs and Named Arguments

Starting with ORM 3.3 you can now create nested DTOs with the `NEW` syntax and
furthermore, use a short named arguments syntax to populate the constructor of
a DTO. This feature was contributed by GitHub user
[eltharin](https://github.com/eltharin) over the last few months and builds
upon previous work.

## Migrations: Fixing a decade old schema comparison bug with PostgreSQL

When you used Doctrine Migrations with the ORM the down migration included a
statement to drop the public schema for the better part of a decade.

This bug has finally been fixed and you won't see this drop schema statement in
newly created migrations for PostgreSQL anymore.

## Psalm and PHPStan going forward

After a long discussion we have decided to only use one static analysis tool
and Doctrine projects will use PHPStan going forward. For now
Psalm checks will be removed from repositories over the next weeks.

This is mainly because PHPStan has outpaced Psalm in depth and quality in the
last few years and it feels unlikely that Psalm can point to a problem that
PHPStan did not detect before. If in the future Psalm catches up to PHPStan we
may reconsider adding it.
