---
title: "Doctrine 2 Preview Release"
menuSlug: blog
layout: blog-post
authorName: romanb
authorEmail:
categories: [release]
permalink: /2009/09/01/doctrine2-preview-release.html
---
Exactly one year ago today we released Doctrine 1.0 stable, which was on
the birthday of Jon. Again, today we have chosen the birthday of Jon to
release the first preview of Doctrine 2. This is an alpha release that
is not intended for production use.

Doctrine 2 marks the beginning of a new approach to ORM with Doctrine.
It represents a rewrite of more than 90% of the existing codebase. The
new key concepts behind Doctrine 2 are to put your domain model more
into focus and to provide transparent persistence where the persistence
is a service and not an inherent property of domain classes. This has a
lot of advantages like decoupling your valuable business logic from the
persistence layer and easy testability of your domain model.

Packages
========

Doctrine 2 is reorganized into reusable layers that are available as
separate packages. The Doctrine 2 Core consists of the following
packages:

-   Doctrine Common (Generic components, high re usability)
-   Doctrine DBAL (The database abstraction layer, includes: Common)
-   Doctrine ORM (The ORM tools, includes: Common + DBAL)

All of these packages are currently distributed and maintained
synchronously which means they are released together and share the same
version numbers. This may change in the future. These three packages are
available as separate downloads even though you will most likely want to
download the ORM package which already contains the Common and DBAL
packages.

Features
========

This Preview Release is mainly about the core ORM functionality and
features of Doctrine 2, like mapping drivers, DQL, association mapping,
inheritance mapping, change tracking, etc. Most of the supporting tools
that you are used to for rapid development like the CLI, migrations,
behaviors and validation have not yet been ported to Doctrine 2 and are
still under heavy development, most of them will end up as loosely
coupled extensions.

Mapping drivers
===============

A mapping driver is a particular strategy for providing ORM metadata to
Doctrine 2. This Preview Release contains mapping drivers for docblock
annotations, XML and YAML. The XML and YAML drivers are still in an
experimental stage and while we encourage you to use them, you may
encounter more issues than with the docblock annotation driver as it is
the primary driver used in our development.

Documentation
=============

The documentation for Doctrine 2 can be found
[here](http://www.doctrine-project.org/documentation/2_0/en). Please be
aware that the documentation is still a work in progress and not all
areas have been completed.

Sandbox
=======

To get started quickly, please check out our [sandbox quickstart
tutorial](http://www.doctrine-project.org/documentation/manual/2_0/en/introduction#sandbox-quickstart).
You can also obtain Doctrine 2.0.0 ALPHA1 via a PEAR package like normal
which can be found on the
[download](http://www.doctrine-project.org/download)

We want to encourage everyone to start experimenting with the new
generation of Doctrine in order to get familiar with it and to help find
any outstanding issues.

As always, please report any issues and feature requests through
[trac](http://trac.doctrine-project.org).

Thank you for using Doctrine.
