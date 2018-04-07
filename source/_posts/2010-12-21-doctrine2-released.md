---
title: Doctrine 2 First Stable Release
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: [release]
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate release of the first stable
Doctrine 2.0 version. This release marks the end of 2.5 years of
dedicated development starting in early 2008 and ending as a christmas
present to our users. We wish everyone a merry christmas!

During the last years a core team of five people contributed large parts
of the code and many developers contributed small patches and features.
In the end the Doctrine 1 code was refactored beyond recognition,
replacing the original ActiveRecord Doctrine 1 with a new DataMapper
implementation. We want to thank all the contributors and early adopters
for all the feedback and discussions.

What is new in Doctrine 2?
==========================

-   DQL is now a real language inside Doctrine, based on an EBNF that is
    parsed and transformed to SQL. Benefits of this refactoring are
    readable error messages, the generation of an AST that allows us to
    support many different vendors and powerful hooks for developers to
    modify and extend the DQL language to their needs. DQL can either be
    written as a string or be generated using a powerful QueryBuilder
    object.
-   Your persistent objects (called entities in Doctrine 2) are not
    required to extend an abstract base class anymore. Doctrine 2 allows
    you to use Plain old PHP Objects.
-   The UnitOfWork is not an alibi-pattern as implemented in Doctrine 1.
    It is the most central pattern in Doctrine 2. Instead of calling
    `save()` or `delete()` methods on your `Doctrine_Record` instances
    you now pass objects to the data mapper object called
    `EntityManager` and it keeps track of all changes until you request
    a synchronisation between database and the current objects in
    memory. This process is very efficient and has consistent semantics.
    This is a significant improvement over Doctrine 1 in terms of
    performance and developer ease-of-use.
-   There are no code-generation steps from YAML to PHP involved in the
    library anymore. YAML, XML, PHP and Doc-Block Annotations are four
    first-class citizens for defining the metadata mapping between
    objects and database. A powerful caching layer allows Doctrine 2 to
    use runtime metadata without relying on code-generation.
-   A clean architecture and powerful algorithms make Doctrine 2
    magnitudes faster than Doctrine 1.
-   Doctrine 2 supports an API that allows you to transform an arbitrary
    SQL statements into an object-structure. This feature is used by the
    Doctrine Query Language itself and is a first-class citizen of the
    library. It essentially allows you to make use of powerful
    vendor-specific features and complex SQL statements without having
    to cirumvent the ORM completely.
-   Inheritance is not akward anymore. There are now three different
    types of inheritance to choose from: Mapped Superclasses,
    Single-Table- and Joined-Table-Inheritance.
-   Many more features, just see the reference guide on what is possible
    with Doctrine 2.

Why did we take so long to develop this new major release?
==========================================================

There are several reasons:

The refactoring of the original Doctrine 1 code marks a paradigm shift
in how we approach object persistence in PHP. Making use of PHP 5.3 only
features we could write an ORM whose internals are much more powerful
than the first version of Doctrine. This meant rewriting lots of
features from scratch and refactoring other code beyond recognition.
Many features were carefully implemented and have been discussed for
weeks or month in our team. We feel that not a single feature in this
release can be called a hack or has negative architectural implications
along the road.

As a user an ORM means committing yourself to a library that you haven't
written yourself and trust it to handle your most important code: The
business and domain logic. We wanted to release a high quality library
and make sure it has no bugs when it is released. This explains why the
first alpha was already released over a year ago and we have been fixing
every little bug that appeared for the last 14 months. When you download
Doctrine 2 now we feel this code is more stable and much more
maintainable than Doctrine 1. The ORM itself has about 1000 tests where
half of these are functional tests that successfully run against all the
supported database vendors MySQL, SQLite, PostgreSQL, Oracle and MSSQL.
The database access layer and common libraries come with an additional
400 tests.

We wanted the release to ship with a complete and well-thought-out
documentation. Writing such a documentation takes time. The current
documentation is probably not perfect, but it contains a very detailed
reference guide and a small tutorial to get started. Additionally there
is a cookbook with several recipes that you can use with your Doctrine 2
project.

While Doctrine 1 had pretty powerful SQL abstraction we felt there were
better libraries out there that could be incoporated into Doctrine 2.
The new database abstraction layer of Doctrine 2 is much more powerful
than the Doctrine 1 DBAL and is powered by code from other great
libraries such as Zeta Components, Zend Framework and PEAR MDB2. On top
of this it can also be used standalone, you can use the DBAL without
having to use the ORM.

Dropping Features of Doctrine 1
===============================

But Doctrine 2 is not only a new version of Doctrine 1. We also dropped
a lot of features that we find inappropriate for the core of an ORM
library:

-   Validators have been dropped. Use a framework library like Zend or
    Symfony for validator support, they ship much more powerful
    validators than Doctrine 1 ever had. If you don't like frameworks
    there is ext/filter to consider.
-   We killed the magic features: Doctrine 2 does not offer behaviors as
    a core feature anymore. We came to the conclusion that behaviors in
    the core lead to the big ball of mud called Doctrine
    1. The code is nearly unmaintainable because of all the special
    logic and magic that works everywhere. That is why Doctrine 2
    focuses on being a consistent and extensible object-relational
    mapper only and behaviors should be released as extensions on top of
    Doctrine 2. While this approach was questioned by many of the
    Doctrine 1 users we think this is the right approach. We are already
    seeing third party libraries and extensions like Doctrator based on
    Doctrine 2 that implement these features.
-   Explicit multiple connection support has been dropped. Use multiple
    instances of `Doctrine\DBAL\Connection` or
    `Doctrine\ORM\EntityManager`. Doctrine 2 uses no global state that
    could affect the usage of multiple instances.

Is Doctrine 2 backwards compatible?
===================================

No it is not. Doctrine 1 and 2 have nothing in common. For what its
worth they only share the same project name. You cannot simply move from
your Doctrine 1.2 to a Doctrine 2 project. Why didn't we release a
backwards compatible ORM? Because we think Doctrine 1 has architectural
flaws that cannot be fixed.

What is the plan for Doctrine 2 beyond this release?
====================================================

With the core that is now Doctrine 2 we plan to keep the library
backwards compatible at all times. Not only for minor and mini-releases
such as 2.0.1 or 2.1, even for potential releases of a Doctrine 3 or 4
version we plan to avoid public API refactorings as much as possible. If
however we feel that there is overwhelming evidence that a public API
refactoring makes the ORM faster and leads to more maintainable code we
will not hesitate to break API for a 3.0 release.

This approach comes at costs that we are willing to pay. All new
features have to pass a requirements discussion and pros/cons are
carefully weighted against each other. That is also why we try to expose
as little of our internals as possible. This certainly hurts
extensibility of Doctrine 2, but with our expected quality level and
review process we hope to bring the costs of this approach down. You
should never be forced to extend Doctrine 2 just to fix bugs, which is
the most important reason for extensibility in other PHP libraries.

Where do I start?
=================

You can download Doctrine 2 [from our downloads
section](http://www.doctrine-project.org/projects/orm/download) ,
[install it via PEAR](http://pear.doctrine-project.org/) or find it in
the [Github repository](http://github.com/doctrine/doctrine2). Symfony 2
also ships with a current version of Doctrine 2. After you installed
Doctrine 2 you can [go to the
documentation](http://www.doctrine-project.org/docs/orm/2.0/en/) and
start reading the reference guide or [the
tutorial](http://www.doctrine-project.org/docs/orm/2.0/en/tutorials/getting-started-xml-edition.html).

If you find any bugs or have feature requests you should check our
[Bug-Tracker and report bugs or feature
requests](http://www.doctrine-project.org/jira). If you want to discuss
about Doctrine 2 you can either [use the Google Group or join the
\#doctrine channel on the Freenode IRC
Network](http://www.doctrine-project.org/community). Also make sure to
check the current [Limitations and Known Issues
section](http://www.doctrine-project.org/docs/orm/2.0/en/reference/limitations-and-known-issues.html)
in the docs. We are trying to be honest about what Doctrine 2 can and
can't do but might do in the future.
