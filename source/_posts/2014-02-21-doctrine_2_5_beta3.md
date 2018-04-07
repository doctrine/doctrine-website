---
title: Doctrine DBAL 2.5 BETA3
menuSlug: blog
authorName: default
authorEmail: 
categories: [dbal]
permalink: /:year/:month/:day/:basename.html
---
We have released the BETA3 of DBAL 2.5 after some more work on the many
new features. For early testers, we have refactored the Exception
support again and removed the constants in favour of a nicely designed
Exception hierarchy. Many other issues were fixed and we hope this will
be the last beta release before a release candidate in early March and a
final release in March as well.

For details about all the new features in DBAL 2.5, see the [previous
release blog
post](http://www.doctrine-project.org/2014/01/01/dbal-242-252beta1.html)
and the [Jira
Release](http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10523).

You can install the BETA through Composer with the following version
constraint::

    {
        "require": {
            "doctrine/dbal": "2.5.0@beta"
        }
    }

DBAL 2.5 is backwards compatible to 2.4 and earlier in everything except
small details (See UPGRADE.md). You can even test Doctrine DBAL 2.5 with
a stable DBAL 2.4 version.

If you find any problems with this beta, please report a bug [on
Jira](http://www.doctrine-project.org/jira).
