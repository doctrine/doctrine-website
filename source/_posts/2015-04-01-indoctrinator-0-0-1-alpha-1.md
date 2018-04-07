---
title: Indoctrinator 0.0.1-alpha1
menuSlug: blog
authorName: Marco Pivetta
authorEmail: ocramius@gmail.com
categories: []
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the start of development on a new project
called **the indoctrinator**.

What is Indoctrinator?
======================

For various months, we tried to implement a way to validate the correct
usage of the Doctrine Project mapping tools. This sort of validation
logic includes:

-   immutability checks/suggestions
-   number of generated DB queries/hits reduction
-   memory impact control
-   hydration profiling
-   code generator avoidance
-   DDD (Domain Driven Development) entity class/method naming
    conventions
-   ... and much more!

We decided to put these validation rules into a project.

How does Indoctrinator work?
============================

Indoctrinator is currently only working with `doctrine/orm` version
`2.5.x-dev`, but the general working concept is as following:

~~~~ {.sourceCode .json}
$indoctrinator = new Doctrine\Indoctrinator();

$indoctrinator->registerWithManager(new Doctrine\Indoctrinator\ManagerWrapper($entityManager));
~~~~

Without going into much details, Indoctrinator hooks into common APIs
used in ORM internals, and by using AOP (Aspect Oriented Programming),
it catches common mistakes and issues and produces exceptions or log
messages that "indoctrinate" the user on correct toolchain usage.

Release RoadMap
===============

Indoctrinator is still in early development, but our plan is to release
it with bindings for major editors and IDEs used in the PHP community.

The current version is `0.0.1-alpha1`, and is released as a `phar`
archive for now.

Development will likely take 6 or more months, while we stabilize the
API and make the various mapper projects compatible with it.

How to get Indoctrinator?
=========================

Indoctrinator has its own dedicated [documentation section in the
doctrine
website](http://www.doctrine-project.org/projects/indoctrinator.html).

Reporting Issues
================

Please report any issues you may have with the project on the mailing
list or on [JIRA](http://www.doctrine-project.org/jira/browse/).
