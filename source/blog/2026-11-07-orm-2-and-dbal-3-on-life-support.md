---
title: "ORM 2 and DBAL 3 are now on Life Support"
authorName: Benjamin Eberlei
authorEmail: kontakt@beberlei.de
permalink: /2026/11/07/orm-2-and-dbal-3-on-life-support.html
---

During our [Core Team Meetup in Amsterdam](/2026/09/16/doctrine-core-team-meetup-amsterdam26.html)
this week, we discussed again how to communicate the state of old release
series, like ORM 2 and DBAL 3, that are still widely used but that we want you
to upgrade away from.

[A year ago](/2025/10/08/an-update-on-the-orm-2-end-of-life.html) we pushed the
ORM 2 End of Life to at least February 2027 and restricted the changes that are
eligible for the 2.x branch. Since then, the website has still been labelling
these versions as "stable", which does not reflect that you should be planning
your upgrade.

That is why we are introducing a new status for release series: **Life Support**.

This also replaces the planned End of Life date: instead of ending support for
ORM 2 in February 2027, we will keep maintaining it under life support
indefinitely, for as long as it is feasible to support new PHP versions.

## What does Life Support mean?

A release series that is on life support is maintained on a best-effort basis.
It only receives:

* Security fixes
* Compatibility fixes for new PHP versions

There will be no new features and no regular bug fixes in these releases
anymore. Life support ends when an old release series cannot be made compatible
with a new version of PHP without breaking backwards compatibility.

The credit for this idea goes to [Sebastian Bergmann](https://sebastian-bergmann.de/),
who has been using this concept for [PHPUnit](https://phpunit.de/supported-versions.html)
for a while now:

> Life Support means that changes will be made for an otherwise unsupported
> version of PHPUnit to be compatible with new versions of PHP. Life Support
> ends when an old version of PHPUnit cannot be made compatible with a new
> version of PHP without breaking backwards compatibility.

We think this is a great way to give users of old versions the confidence that
their code keeps running on new PHP versions, while making it clear that
the release series is not actively developed anymore.

## Which versions are on Life Support?

Starting today, the following release series are on life support:

* [ORM 2.20.x](/projects/orm.html)
* [DBAL 3.10.x](/projects/dbal.html)

You will see the "life support" label in the versions table on the project
pages, in the version switcher of the documentation, and as an alert when
browsing the documentation of these versions.

## What should you do?

If you are still on ORM 2 or DBAL 3, please plan your upgrade to ORM 3 and
DBAL 4. The [upgrade guides to DBAL 4](https://github.com/doctrine/dbal/blob/4.5.x/UPGRADE.md)
and [upgrade guides to ORM 3](https://github.com/doctrine/orm/blob/3.7.x/UPGRADE.md)
contain all the information about the changes you need to make.

If something is blocking you from upgrading, please let us know in the
discussion [What is blocking you from upgrading from ORM 2 to ORM 3? \#12193](https://github.com/doctrine/orm/discussions/12193).
