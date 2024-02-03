---
title: "Doctrine ORM 3 and DBAL 4 Released"
authorName: Jonathan H. Wage
authorEmail: jwage@doctrine-project.org
permalink: /2024/02/03/doctrine-orm-3-and-dbal-4-released.html
---

We are thrilled to announce the release of Doctrine ORM 3.0 and DBAL 4.0.
These releases are the culmination of over a decade of hard work across
dozens of contributors and the Doctrine maintainers.

## What's New

**A Slimmer, More Efficient ORM**: The new Doctrine ORM 3.0 comes in at
326KB, down from 400KB in ORM 2.18.0. This reduction not only makes the
ORM lighter but also signals our efforts to streamline and optimize every
aspect of our library.

**Enhanced Code Quality and Coverage**: With ORM 3.0, we've pushed our
code coverage from 84% to 89%. This improvement underscores our commitment
to reliability and the stability of the Doctrine ecosystem, ensuring that
your applications run smoothly.

**Leaner Dependencies**: In Doctrine ORM 3.0, we have finally eliminated
dependencies on `doctrine/cache` and `doctrine/common`. This change reduces
complexity and improves maintainability of Doctrine as we now depend on other
well maintained libraries for caching responsibilities.

**A Growing Community**: The Doctrine project now boasts 1029 contributors
across all its projects. This vibrant community is the backbone of Doctrine,
providing valuable insights, feedback, and contributions that drive the
project forward.

## Upgrading

We understand that upgrading to a new major version can be difficult. The
best way to upgrade is to first upgrade to the latest Doctrine ORM 2.x and
DBAL 3.x version and address any deprecation warnings that are reported.
Once you have addressed all of the deprecations, you should have a clear
path to upgrade.

In addition to that, we've prepared comprehensive upgrade guides to facilitate
a smooth transition to ORM 3.0 and DBAL 4.0. These guides offer detailed
information about all the deprecations and changes we've made.

- [Upgrade to Doctrine ORM 3.0](https://github.com/doctrine/orm/blob/3.0.x/UPGRADE.md)
- [Upgrade to Doctrine DBAL 4.0](https://github.com/doctrine/dbal/blob/4.0.x/UPGRADE.md)

## Looking Forward

ORM 3 and DBAL 4 are a big step forward towards modernizing the API of our
libraries, increasing safety with the use of scalar types in the code base,
better error handling and generally cleaning up the code to make it easier
to maintain. We look forward to continuing work on Doctrine and focusing on
being the most stable and reliable PHP database persistence related libraries
available.
