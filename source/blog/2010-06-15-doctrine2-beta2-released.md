---
title: "Doctrine2 BETA2 Released"
authorName: beberlei
authorEmail:
categories: [release]
permalink: /2010/06/15/doctrine2-beta2-released.html
---
Today we are happy to announce the immediate availability of the second
beta version of Doctrine2. This is the first release after the
[split](https://www.doctrine-project.org/2010/05/27/bringing-it-all-together.html)
of Doctrine2 into three independent projects, Common, DBAL and ORM. This
change took longer than we expected but we are back to our SVN
productivity now and strive to surpass it utilizing Git.

Beta 2 is a completely backwards compatible release and over 60 issues
and bugs have been closed in total. The following larger changes have
been introduced from Doctrine2 Beta 1:

Common
======

-   Added ClassLoader\#classExists as well as
    ClassLoader\#getClassLoader methods
    [DCOM-7](https://github.com/doctrine/common/issues/669)
-   Changes to Annotation Parser with regards to Autoloading Annotation
    Classes

DBAL
====

-   New Driver support for Microsoft PDO Sqlsrv Extension
    [DBAL-10](https://github.com/doctrine/dbal/issues/927)
-   Fixed Mssql/Sqlsrv Platforms and SchemaManager
    [DBAL-8](https://github.com/doctrine/dbal/issues/2031)
-   New Driver and Platform Support for DB2 (PDO\_DB2 and IBM\_DB2
    Extensions)
    [DDC-494](https://github.com/doctrine/orm/issues/4999)

ORM
===

-   Basic Pessimistic Locking support using FOR UPDATE or vendor
    specific shared locks
    [DDC-178](https://github.com/doctrine/orm/issues/2432)
-   Added a Validate Mapping CLI Task
    [DDC-515](https://github.com/doctrine/orm/issues/5023)

Download
--------

You can get the code a few different ways which are described in detail
[here](http://www.doctrine-project.org/projects/orm/2.0/download/2.0.0BETA2).
If you have any issues with Doctrine you can report them on
[Jira](http://www.doctrine-project.org/jira).

Contributions
-------------

We thank all the contributors and early adopters for their extensive
feedback and reports. If you are interesting in contributing to the
Doctrine project too, check out our new [contributors
guide](http://www.doctrine-project.org/contribute) and
[community](http://www.doctrine-project.org/community) page for
information about how you can get involved!
