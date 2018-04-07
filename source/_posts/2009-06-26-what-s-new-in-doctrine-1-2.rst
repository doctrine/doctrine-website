---
title: What's new in Doctrine 1.2
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
This week I have been working on the next major version of
Doctrine, 1.2. This version will possibly be the last 1.x version
before we really begin to push 2.0 hard.

The 1.2 release should be a decent release for you all and I think
it has some interesting features that should really spark some
growth in the community around Doctrine. Here are some highlights.

Highlights
----------


-  Major cleanup, removing deprecated methods, removed accessor
   string support for performance fix
-  Option to disable cascading saves by default for performance
   improvement
-  Changes to migrations to better handle migrating multiple
   databases
-  More configuration options
-  Configure a child ``Doctrine_Query`` class to use
-  Configure a child ``Doctrine_Collection`` class to use
-  Refactored hydration implementation to be completely driver
   based
-  Write your own hydration drivers to process query statements
-  Refactored Doctrine connections to be completely driver based
-  Write your own connection drivers for Doctrine
-  Other small changes and improvements across the code base

Doctrine Extensions
-------------------

All the above changes lend themselves well to creating extensions
and behaviors for Doctrine. This has led to the creation of the
Doctrine `extensions <http://www.doctrine-project.org/extensions>`_
repository.

You can now write standalone code bundled as a Doctrine extension
that can be dropped in to an extensions folder and loaded by
Doctrine. So now when you all write custom behaviors and custom
extensions you can make them available for other people to use and
drop in to projects.

I have started the repository by committing the
`Sortable <http://www.doctrine-project.org/extension/Sortable/1_2-1_0>`_
behavior which was contributed by a Doctrine user on our trac. So
whoever you are, if you would like to maintain this extension
please contact me.

Follow 1.2 Development
----------------------

If you want to know more about Doctrine 1.2 you can read the
`What's new in Doctrine 1.2 <http://www.doctrine-project.org/upgrade/1_2>`_
document which will be kept up to date as we develop and change
things in 1.2!

    **CAUTION** Some of the implemented features in 1.2 are still being
    discussed and debated so they may change or be completely removed
    if decided so. If you have any input or suggestions on the provided
    features, let us know ASAP.
