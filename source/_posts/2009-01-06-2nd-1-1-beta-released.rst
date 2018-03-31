---
title: 2nd 1.1 Beta Released!
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: [release]
indexed: false
---
Today we are happy to introduce the 2nd beta version of the 1.1
branch. This means we are getting very close to delivering a stable
version to you.

Development Highlights
^^^^^^^^^^^^^^^^^^^^^^


-  `5311 <http://trac.doctrine-project.org/changset/5311>`_ - Fixed
   infinite loop problem with isValid()
-  `5336 <http://trac.doctrine-project.org/changeset/5336>`_ -
   Added $deep argument for isValid() and isModified() and defaulted
   it to false to maintain BC
-  `5337 <http://trac.doctrine-project.org/changeset/5337>`_ -
   Removes used of \_identifiers in synchronizeWithArray()
-  `5339 <http://trac.doctrine-project.org/changeset/5339>`_ -
   Reverting `5084 <http://trac.doctrine-project.org/changeset/5084>`_
   as it introduces some bugs which cannot be fixed until a later
   version
-  `5341 <http://trac.doctrine-project.org/changeset/5341>`_ -
   Fixed issue with named parameters
-  `5344 <http://trac.doctrine-project.org/changeset/5344>`_ -
   Added checking for obj.field IN :named to prevent possible issues
   of DQL
-  `5345 <http://trac.doctrine-project.org/changeset/5345>`_ -
   Fixed issue with temporary generated models not being cleaned up
   properly

You can also check out the
`detailed documentation <http://www.doctrine-project.org/upgrade/1_1>`_
of all the changes that are contained in the 1.1 version of
Doctrine as well as the
`changelog <http://www.doctrine-project.org/change_log/1_1_0_BETA2>`_
to help ease the upgrade process.
