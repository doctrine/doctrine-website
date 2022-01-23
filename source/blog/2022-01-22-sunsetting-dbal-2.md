---
title: Sunsetting Doctrine DBAL 2
authorName: Sergei Morozov
authorEmail: morozov@tut.by
permalink: /2022/01/22/sunsetting-dbal-2.html
---

Since the release of [Doctrine DBAL 3.0.0 ](https://github.com/doctrine/dbal/releases/tag/3.0.0) in November 2020,
the 2.x release series effectively went into the maintenance mode. In the past year, we've been accepting mostly
the following types of patches for DBAL 2:

1. Development dependency updates
2. Security fixes
3. Improvements to compatibility with PHP 8.1
4. Improvements in the upgrade path to DBAL 3

Except for dependency updates, at the moment, there are no known issues in DBAL 2 that would fall into any of
the above categories.

Many projects that depend on Doctrine DBAL depend on it indirectly via Doctrine ORM which until
[release 2.10.0](https://github.com/doctrine/orm/releases/tag/2.10.0) didn't support DBAL 3.
It was one of the blockers of the DBAL 3 adoption which is no longer the case.

With all that said, the DBAL team announces the plan for sunsetting DBAL 2 in 6 months as of the ORM 2.10.0 release
which is April 3, 2022. After that date, we plan to release DBAL 2 only to address security related
and other critical issues for at most a year.

All Doctrine DBAL users are encourarged to upgrade to the latest stable version
which is [3.3.0](https://github.com/doctrine/dbal/releases/tag/3.3.0) as of the time of this writing.
