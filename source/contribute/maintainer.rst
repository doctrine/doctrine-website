---
layout: default
title: Maintainer Workflow
menuSlug: development
permalink: /contribute/maintainer/index.html
---

Maintainer Workflow
===================

Who is a maintainer? Maintainers are those who have been granted write
access to the main repository of a project. In the example of the ORM,
it would be this `repository <http://github.com/doctrine/doctrine2>`_.
This repository will be referred to as **doctrine** in this document.

You might want want to know how a maintainer is different from a
contributor. The **Maintainer Workflow** is used primarily for the
following:

-  Merging **contributor** branches into **doctrine/master** and/or
   **doctrine/\*** release branches.
-  Creating release branches.
-  Tagging released versions within **master** and release branches.

Setup
-----

First you must Fork the
`repository <http://github.com/doctrine/doctrine2>`_ and clone your fork
locally:

.. code-block:: console

    $ git clone git@github.com:username/doctrine2.git doctrine2-orm
    $ cd doctrine2-orm

Fetch dependencies using `composer <https://getcomposer.org/>`_:

.. code-block:: console

    $ composer install

Now add the **doctrine** remote for maintainers:

.. code-block:: console

    $ git remote add doctrine git@github.com:doctrine/doctrine2.git

Adjust your branch to track the doctrine master remote branch, by
default it'll track your origin remote's master:

.. code-block:: console

    $ git config branch.master.remote doctrine

Optionally, add any additional contributor/maintainer forks, e.g.:

.. code-block:: console

    $ git remote add romanb git://github.com/romanb/doctrine2.git

Branching Model
---------------

Merging topic branches:

-  Topic branches **must** merge into **master** and/or any affected
   release branches.
-  Merging a topic branch puts it into the *next* release, that is the
   next release created from **master** and/or the next patch release
   created from a specific release branch.

Configuring Remotes
-------------------

Add remote repo for contributor/maintainer, if necessary (only needs to
be done once per maintainer):

.. code-block:: console

    $ git remote add hobodave git://github.com/hobodave/doctrine2.git

Fetch remote:

.. code-block:: console

    $ git fetch hobodave

Merge topic branch into master:

.. code-block:: console

    $ git checkout master
    Switched to branch 'master'
    $ git merge --no-ff hobodave/DDC-588
    Updating ea1b82a..05e9557
    (Summary of changes)
    $ git push doctrine master

The **--no-ff** flag causes the merge to always create a new commit
object, even if the merge could be performed with a fast-forward. This
avoids losing information about the historical existence of a topic
branch and groups together all commits that together added the topic.

Release Branches
----------------

-  May branch off from: **master**
-  Must merge back into: **master**
-  Branch naming convention: 1.0, 2.0, 2.1

Release branches are created when **master** has reached the state of
the next major or minor release. They allow for continuous bug fixes and
patch releases of that particular release until the release is no longer
supported.

The key moment to branch off a new release branch from **master** is
when **master** reflects the desired state of the new release.

Creating a release branch
^^^^^^^^^^^^^^^^^^^^^^^^^

Release branches are created from the **master** branch. When the state
of **master** is ready for the upcoming target version we branch off and
give the release branch a name reflecting the target version number. In
addition the ".0" release is tagged on the new release branch:

.. code-block:: console

    $ git checkout -b 2.0 doctrine/master
    Switched to a new branch "2.0"
    $ git push doctrine 2.0
    $ git tag -a 2.0.0
    $ git push doctrine 2.0

This new branch may exist for a while, at least until the release is no
longer supported. During that time, bug fixes are applied in this branch
(in addition to the **master** branch), if it is affected by the same
bug. Adding large new features here is prohibited. They must be merged
into **master**, and therefore, wait for the next major or minor
release.
