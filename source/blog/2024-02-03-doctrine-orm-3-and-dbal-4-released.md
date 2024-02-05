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
aspect of our library and focus our maintenance efforts on the core functionality
of an ORM and less on tooling and helpers that are only useful by a small number
of our users.

**Enhanced Code Quality and Coverage**: With ORM 3.0, we've pushed our
code coverage from 84% to 89%. For DBAL 4.0, we've pushed our code coverage
from 86% to 94%. This improvement underscores our commitment to reliability
and the stability of the Doctrine ecosystem, ensuring that your applications
run smoothly.

**Leaner Dependencies**: In Doctrine ORM 3.0, we have finally eliminated
dependencies on `doctrine/cache` and `doctrine/common`. This change reduces
complexity and improves maintainability of Doctrine as we now depend on
[PSR-6: Caching Interface](https://www.php-fig.org/psr/psr-6/) for our
caching responsibilities. Implementing a PSR means we are more interoperable
with other frameworks and easier to use by a broader amount of users.

**A Growing Community**: The Doctrine project now boasts 1029 contributors
across all its projects. This vibrant community is the backbone of Doctrine,
providing valuable insights, feedback, and contributions that drive the
project forward.

## Upgrading

We understand that upgrading to a new major version can be difficult. The
best way to upgrade is to first upgrade to the latest Doctrine ORM 2.x and
DBAL 3.x version and address any deprecation warnings that are reported. You
can read more about how Doctrine handles deprecations [here](https://www.doctrine-project.org/policies/deprecation.html).
Once you have addressed all of the deprecations, you should have a clear
path to upgrade.

In addition to that, we've maintained comprehensive documentation about
every change, deprecation and BC break to facilitate a smooth transition
to ORM 3.0 and DBAL 4.0.

- [Upgrade to Doctrine ORM 3.0](https://github.com/doctrine/orm/blob/3.0.x/UPGRADE.md)
- [Upgrade to Doctrine DBAL 4.0](https://github.com/doctrine/dbal/blob/4.0.x/UPGRADE.md)

## The Future of Doctrine ORM 2

We plan to maintain Doctrine ORM 2 for at least the next 2 years by providing
bug and security fixes. We may also add or deprecate things in 2.x to improve
the existing forward-compatbility layer to make the transition to ORM 3 smoother.

## Looking Forward

ORM 3 and DBAL 4 are a big step forward towards modernizing the API of our
libraries, increasing safety with the use of scalar types in the code base,
better error handling and generally cleaning up the code to make it easier
to maintain. We look forward to continuing work on Doctrine and focusing on
being the most stable and reliable PHP database persistence related libraries
available.
