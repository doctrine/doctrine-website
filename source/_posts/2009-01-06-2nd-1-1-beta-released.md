---
title: 2nd 1.1 Beta Released!
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: [release]
permalink: /2009/01/06/2nd-1-1-beta-released.html
---
Today we are happy to introduce the 2nd beta version of the 1.1 branch.
This means we are getting very close to delivering a stable version to
you.

Development Highlights
======================

-   [5311](http://trac.doctrine-project.org/changset/5311) - Fixed
    infinite loop problem with isValid()
-   [5336](http://trac.doctrine-project.org/changeset/5336) -Added
    \$deep argument for isValid() and isModified() and defaulted it to
    false to maintain BC
-   [5337](http://trac.doctrine-project.org/changeset/5337) -Removes
    used of \_identifiers in synchronizeWithArray()
-   [5339](http://trac.doctrine-project.org/changeset/5339) -Reverting
    [5084](http://trac.doctrine-project.org/changeset/5084) as it
    introduces some bugs which cannot be fixed until a later version
-   [5341](http://trac.doctrine-project.org/changeset/5341) -Fixed issue
    with named parameters
-   [5344](http://trac.doctrine-project.org/changeset/5344) -Added
    checking for obj.field IN :named to prevent possible issues of DQL
-   [5345](http://trac.doctrine-project.org/changeset/5345) -Fixed issue
    with temporary generated models not being cleaned up properly

You can also check out the [detailed
documentation](http://www.doctrine-project.org/upgrade/1_1) of all the
changes that are contained in the 1.1 version of Doctrine as well as the
[changelog](http://www.doctrine-project.org/change_log/1_1_0_BETA2) to
help ease the upgrade process.
