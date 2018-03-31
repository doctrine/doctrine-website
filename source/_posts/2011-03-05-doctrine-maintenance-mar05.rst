---
title: Maintenance Releases 2.0.2 DBAL and ORM
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: [release]
indexed: false
---
Slightly delayed but here are the releases of DBAL and ORM versions
2.0.2:


-  `ORM 2.0.2 Changeset <http://www.doctrine-project.org/jira/browse/DDC/fixforversion/10116>`_
-  `DBAL 2.0.2 Changeset <http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10118>`_

A total of 22 issues was fixed.

There was one big change in the build mechanism. Symfony YAML and
Console dependencies are now converted to git submodules and are
also shipped as their own PEAR packages (DoctrineSymfonyYaml and
DoctrineSymfonyConsole).
