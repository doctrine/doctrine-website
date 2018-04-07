---
title: Doctrine ORM 2.5.1 and 2.4.8 released
menuSlug: blog
authorName: default
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate availability of Doctrine ORM 2.5.1 and
2.4.8.

This versions include a fix for the `Security Misconfiguration Vulnerability
<http://www.doctrine-project.org/2015/08/31/security_misconfiguration_vulnerability_in_various_doctrine_projects.html>`_
described in an earlier blog post today.

Here are the changelogs:

Changelog 2.5.1
---------------

- DCOM-293: Fix for Security Misconfiguration Vulnerability
- DDC-3831: Fixed issue when paginator orders by a subselect expression
- DDC-3699: Fix bug in EntityManager#merge: Skipping properties if they are listed after a not loaded relation
- DDC-3684: Fix bug ClassMetadata#wakeupReflection when used with embeddables and Static Reflection
- DDC-3683: SecondLevelCache: Fix bug in DefaultCacheFactory#buildCollectionHydrator()
- DDC-3667: PersistentCollection: Fix BC break when creating empty Array/PersistentCollections

Changelog 2.4.8
---------------

This release contains several fixes that have been in 2.5.0 already and are
just backported to 2.4 for convenience. This is the last release in the 2.4
branch and you should upgrade to 2.5.

- DCOM-293: Fix for Security Misconfiguration Vulnerability
- DDC-3551: Fix difference between DBAL 2.4 and 2.5 concerning platform initialization and version detection.
- DDC-3240: EntityGenerator: Fix inheritance in Code-Generation
- DDC-3502: EntityGenerator: Fixed parsing for php 5.5 "::class" syntax
- DDC-3500: Joined Table Inheritance: Fix applying ON/WITH conditions to first join in Class Table Inheritance
- DDC-3343: Entities should not be deleted when using EXTRA_LAZY and one-to-many
- DDC-3619: Bugfix: Prevent Identity Map Garbage Collection that can cause spl_object_hash collisions
- DDC-3608: EntityGenerator: Properly generate default value from yml & xml mapping
- DDC-3643: EntityGenerator: Fix EntityGenerator RegenerateEntityIfExists not working correctly.

As usual you can grab the latest versions from Composer.
