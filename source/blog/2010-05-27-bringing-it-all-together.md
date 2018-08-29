---
title: "Bringing it all together"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: jwage
authorEmail:
categories: []
permalink: /2010/05/27/bringing-it-all-together.html
---
Recently we've been making some pretty serious changes around here. This
blog post aims to overview it all and make sure people are aware of what
is going on!

Website Changes
===============

A few weeks ago I made some
[updates](http://www.doctrine-project.org/blog/a-few-website-changes) to
the Doctrine website. Some said it looked like we took a step backwards.
Well, we kind of did. I had to rip apart the website, remove old legacy
code and reorganize things so that it can be integrated with the changes
we wanted to make next. So I've sort of had to tear it all apart and put
it back together a new way. It's been a bumpy road but we're starting to
get the remaining issues worked out.

The Switch to git
=================

The next big change we wanted to make was to switch to
[github.com](http://www.doctrine-project.org) for our source control.
This has a lot of impact on the project since everything is built around
the source control, including the website. That is why the previous
change was necessary in order to make this move complete.

Splitting Doctrine 2 Sources
============================

The next step for us was to split the Doctrine 2 sources into separate
repositories on github. So now the source code of the Common, DBAL, and
ORM packages are truly separated.

-   [Common](http://github.com/doctrine/common)
-   [DBAL](http://github.com/doctrine/dbal)
-   [ORM](http://github.com/doctrine/doctrine2)

This means the packages can evolve independently and will be maintained
separately from now on. The website also has dedicated project pages for
the [DBAL](http://www.doctrine-project.org/projects/dbal) and
[ORM](http://www.doctrine-project.org/projects/orm) with plenty of
documentation.

Bringing it all together
========================

The last piece that brings it all together is the new [Guide for
Doctrine Contributors and
Collaborators](http://www.doctrine-project.org/contribute). Now that
we've moved to git we had to reinvent a lot of stuff we had already
learned for SVN. Thanks to our git mentor David Abdemoulaie
([hobodave](http://www.twitter.com/hobodave)) we have figured out a
workable solution for dealing with the project dependencies via git
submodules that does most of what we want. Through this process we all
worked on some new documentation that detailed how contributors and
collaborators for the Doctrine Project should work. We really hope that
this will help get people involved with the project and mature things
more.
