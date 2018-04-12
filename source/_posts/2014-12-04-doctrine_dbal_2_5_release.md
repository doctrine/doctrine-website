---
title: Doctrine DBAL 2.5 Release
menuSlug: blog
authorName: Benjamin Eberlei 
authorEmail: 
categories: [release]
permalink: /2014/12/04/doctrine_dbal_2_5_release.html
---
We are happy to announce the immediate availability of Doctrine DBAL
2.5.0 Stable. This release is a landmark in making DBAL more consistent
and stable than it ever was.

Starting with 2.5 DBAL will have a new release-master, [Steve
Müller](https://github.com/deeky666). Steve has been working on DBAL for
the last two years and contributed many of the big features that make up
this fantastic release. He will replace Benjamin Eberlei who was the
release master since version 2.0.

You can install DBAL through Composer:

This version is mostly backwards compatible with only some minor breaks,
when using PDO IBM, creating a custom platform or using non-default
DateTime formats. You can see the breaks in the
[UPGRADE.md](https://github.com/doctrine/dbal/blob/master/UPGRADE.md)
file. DBAL 2.5 is also compatible with ORM 2.4, there is no need to wait
for ORM 2.5 to be released.

The following list contains the major new features of DBAL:

-   Support for SAP Sybase SQL Anywhere versions 10, 11, 12 and 16
    (DBAL-475)
-   Support for auto-commit=NO on all drivers and platforms. (DBAL-81)
-   Refactor exceptions to use common error-codes and exception classes
    that derive from `Doctrine\DBAL\DBALException`. (DBAL-407)
-   Add support to retry connections with
    `Doctrine\DBAL\Connection#ping()` method. (DBAL-275)
-   Add INSERT support to QueryBuilder (DBAL-320)
-   Add Binary type for VARBINARY support (DBAL-714)
-   Add charset and SSL connection support to PostgreSQL (DBAL-567,
    DBAL-702)
-   Add options support for Myqli (DBAL-643)
-   Add support for using database uris with the `url` parameter in
    `DriverManager::getConnection()` which supports several forms used
    by PaaS providers out of the box (DBAL-1050).
-   Auto detection of platform to use based on database (DBAL-757)
-   Improved SQL Server LIMIT emulation support. (Multiple tickets)
-   Improvements to HHVM Support (no full support yet)

See all the changes for the 2.5.0 Release on Jira, an amazing total of
316 issues:

-   [DBAL
    2.5.0](http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10523/)

If you find any problems or backwards compatibility breaks with this
release please report them on JIRA or open up Pull Requests on Github.
