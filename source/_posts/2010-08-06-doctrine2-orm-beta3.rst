---
title: Doctrine ORM Beta 3 released
menuSlug: blog
authorName: romanb 
authorEmail: 
categories: [release]
indexed: false
---
We would like to announce the immediate release of Doctrine ORM
BETA 3:


-  `Installation <http://www.doctrine-project.org/projects/orm/2.0/download/2.0.0BETA3>`_
-  `Changelog <http://www.doctrine-project.org/jira/browse/DDC/fixforversion/10060>`_

We fixed 45 issues, most of them bugs reported by our users. The
ORM package is now in a state where no new features will be added
and we will fully concentrate the efforts on fixing bugs and
bringing the release to a stable state.

Notable changes include:


-  ``EntityManager#merge()`` has been improved considerably and now
   also accepts new entities.
-  Uninitialized Proxies can now be serialized and only throw an
   exception when they are unserialized and accessed without being
   merged into the EntityManager.
-  New method ``EntityManager#getPartialReference()`` that returns
   a partial entity that only contains the entities primary key and
   won't lazy-load (hence "partial").

This release contains three backwards incompatible changes you
should know about when upgrading:

Changed SQL implementation of Postgres and Oracle DateTime types
----------------------------------------------------------------

The DBAL Type "datetime" included the Timezone Offset in both
Postgres and Oracle. As of this version they are now generated
without Timezone (TIMESTAMP WITHOUT TIME ZONE instead of TIMESTAMP
WITH TIME ZONE). See
`this comment to Ticket DBAL-22 <http://www.doctrine-project.org/jira/browse/DBAL-22?focusedCommentId=13396&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#action_13396>`_
for more details as well as migration issues for PostgreSQL and
Oracle.

Both Postgres and Oracle will throw Exceptions during hydration of
Objects with "DateTime" fields unless migration steps are taken!

Removed multi-dot/deep-path expressions in DQL
----------------------------------------------

The support for implicit joins in DQL through the multi-dot/Deep
Path Expressions was dropped. For example:

::

    SELECT u FROM User u WHERE u.group.name = ?1

"u.group.name" is a nested path expression that is an implicit
join. Internally the DQL parser would rewrite these queries to:

::

    SELECT u FROM User u JOIN u.group g WHERE g.name = ?1

This explicit notation will be the only supported notation as of
now. The internal handling of nested path expressions for implicit
joins in the DQL Parser was too complex and error prone in edge
cases and required special treatment for several features we added.
Note that this does not remove any existing functionality, only a
convenience notation that can be expressed otherwise. Hence the
generated SQL of both notations is exactly the same.

Default Allocation Size for Sequences
-------------------------------------

The default allocation size for sequences has been changed from 10
to 1. This step was made to not cause confusion with users and also
because it is partly some kind of premature optimization.

What next
---------

It seems likely that we will not be able to hold the anticipated
release date of September 1st for the final release. Instead
September 1st will likely see the last beta release, BETA 4, after
which we hope to enter the release candiates soon, followed by the
stable release.
