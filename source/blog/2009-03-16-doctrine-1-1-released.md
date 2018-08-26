---
title: "Doctrine 1.1 Released"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: jwage
authorEmail:
categories: [release]
permalink: /2009/03/16/doctrine-1-1-released.html
---
Today I am very pleased to bring news to you that Doctrine 1.1.0 stable
is available. This is a significant release for the 1.x code base. It
contains dozens of new features and bug fixes. The 1.1 test suite now
has **0** fails in the test suite compared to 1.0 having **12**! We
recommend you upgrade your projects.

> **NOTE** Some have asked whether 1.1 has all the same bug fixes that
> are in 1.0. The answer is yes. We are committed to maintaining both
> branches and will continue committing fixes to all branches when it
> applies.

Highlights
==========

-   New hydration methods
-   New migration diff tool
-   Better custom accessor/mutator support and integration with
    fromArray() and toArray()
-   Improvements to getModified(), toArray(), fromArray(),
    synchronizeWithArray()
-   Improvements to core behaviors Searchable, SoftDelete, Versionable
-   Dozens of small improvements and additions across the api
-   Plenty of other bug fixes

You can read a detailed list of all the changes made in 1.1
[here](http://www.doctrine-project.org/upgrade/1_1) in the upgrade file.

Download
========

As always you can get Doctrine on the
[download](http://www.doctrine-project.org/download) page or via pear.

    $ pear install pear.phpdoctrine.org/Doctrine-1.1.0

You can also check it out via svn.

    $ svn co http://svn.doctrine-project.org/tags/1.1.0/lib doctrine

If you find any problems with this release please report it on our
[trac](http://trac.doctrine-project.org) or if you have any questions
you can send it to one of our [mailing
lists](http://www.doctrine-project.org/community).
