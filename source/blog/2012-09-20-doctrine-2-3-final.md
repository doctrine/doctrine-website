---
title: "Doctrine 2.3 final relased"
authorName: Benjamin Eberlei
authorEmail:
categories: []
permalink: /2012/09/20/doctrine-2-3-final.html
---
**20.9.2012**

After a 2 month long phase of beta and release candidates we can finally
announce the release of Doctrine 2.3. This includes new versions for the
packages Common, DBAL and ORM.

This release trys to keep backwards compatibility to every previous
release as much as possible, however some slight changes might be
necessary to your applications. See the UPGRADE files of each project
for details:

-   [ORM](https://github.com/doctrine/orm/blob/master/UPGRADE.md)
-   [DBAL](https://github.com/doctrine/dbal/blob/master/UPGRADE.md)

Compared to previous versions there are no new blockbuster feature, but
many little optimizations:

-   Custom ID Generators
-   Naming Strategies (Camel-, Underscore Cased)
-   Collection Criteria API
-   @AssociationOverride and @AttributeOverride (useful for Trait and
    MappedSuperclass)
-   Arbitrary JOIN Syntax (FROM User u JOIN Comment c WITH c.user =
    u.id)
-   Named Native Queries

The complete changelogs are listed on JIRA:

-   [Common
    Changelog](https://www.doctrine-project.org/jira/browse/DCOM/fixforversion/10183)
-   [DBAL
    Changelog](https://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10184)
-   [ORM
    Changelog](https://www.doctrine-project.org/jira/browse/DDC/fixforversion/10185)

We will flesh out the documentation and information about all new
features in the coming month. If you want to contribute to the
documentation of new features see the
[DBAL](https://github.com/doctrine/dbal-documentation) and
[ORM](https://github.com/doctrine/orm-documentation) documentation links
on Github.

You can install the final release through
[Github](https://github.com/doctrine/orm) or
[Composer](https://packagist.org):

    {
        "require": {
            "doctrine/orm": "2.3.0"
        }
    }

The downloadable packages will be available later today.
