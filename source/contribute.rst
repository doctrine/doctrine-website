---
layout: default
title: Contribute
menuSlug: contribute
---

.. raw:: html
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ site.url }}/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contribute</li>
        </ol>
    </nav>

Welcome to the Doctrine Project Contributors Guide. This
documentation aims to document how contributors and collaborators
should work when using git, development workflow, build process,
dependency management, etc.

About
-----

The Doctrine Project is the home of a selected set of PHP libraries
primarily focused on providing persistence services and related
functionality. Its prize projects are a Object Relational Mapper
and the Database Abstraction Layer it is built on top of. You can
view a list of all
`projects <http://www.doctrine-project.org/projects>`_ on the
website.

Contributors vs Collaborators
-----------------------------

Before continuing you need to understand the difference between a
contributor and a collaborator.


-  Contributor: A contributor is someone from the outside not on
   the core development team of the project that wants to contribute
   some changes to a project.
-  Collaborator: A collaborator is someone on the core development
   team of the project and has commit access to the main repository of
   the project.

Continue reading to learn about the workflow for both contributors
and collaborators.

Contributor Workflow
====================

Who is a contributor?
---------------------

A contributor can be anyone! It could be you. Continue reading this
section if you wish to get involved and contribute back to a
Doctrine project.

Initial Setup
-------------


-  Setup a `github <http://github.com>`_ account.
-  Fork the `repository <http://github.com/doctrine/doctrine2>`_ of
   the project you want to contribute to.
-  Clone your fork locally

-

::

    $ git clone git@github.com:username/doctrine2.git


-  Enter the doctrine2 directory and add the **doctrine** remote

-

::

    $ cd doctrine2
    $ git remote add doctrine git://github.com/doctrine/doctrine2.git


-  Adjust your branch to track the doctrine master remote branch,
   by default it'll track your origin remote's master:

-

::

    $ git config branch.master.remote doctrine

Keeping your master up-to-date!
-------------------------------

Once all this is done, you'll be able to keep your local master up
to date with the simple command:

::

    $ git checkout master
    $ git pull --rebase

Alternatively, you can synchronize your master from any branch with
the full fetch/rebase syntax:

::

    $ git fetch doctrine
    $ git rebase doctrine/master master

Using rebase pull will do a rebase instead of a merge, which will
keep a linear history with no unnecessary merge commits. It'll also
rewind, apply and then reapply your commits at the HEAD.

Branching Model
---------------

The following names will be used to differentiate between the
different repositories:


-  **doctrine** - The "official" Doctrine repository
-  **origin** - Your fork of the official repository on github
-  **local** - This will be your local clone of **origin**

As a **contributor** you will push your completed **local** topic
branch to **origin**. As a **contributor** you will pull updates
from **doctrine**. As a **collaborator** (write-access) you will
merge branches from contributors into **doctrine**.

Primary Branches
----------------

The **doctrine** repository holds the following primary branches:


-  **doctrine/master** Development towards the next release.
-  **doctrine/release-\*** Maintenance branches of existing
   releases.

These branches exist in parallel and are defined as follows:

**doctrine/master** is the branch where the source code of **HEAD**
always reflects the latest version. Each released stable version
will be a tagged commit in a **doctrine/release-\*** branch. Each
released unstable version will be a tagged commit in the
**doctrine/master** branch.

    **NOTE** You should never commit to your forked **origin/master**.
    Changes to **origin/master** will never be merged into
    **doctrine/master**. All work must be done in a **topic branch**,
    which are explained below.


Topic Branches
--------------

Topic branches are for contributors to develop bug fixes, new
features, etc. so that they can be easily merged to **master**.
They must follow a few simple rules as listed below:


-  May branch off from: **master** or a **release-\*** branch.
-  Must merge back into: **master** and any affected **release-\***
   branch that should get the same changes, but remember that release
   branches usually only get bug fixes, with rare exceptions.
-  Branch naming convention: anything except **master** and
   **release-\***. If a topic is related to a JIRA issue then the
   branch should be named after that ticket, e.g. **DDC-588**

Topic branches are used to develop new features and fix reported
issues. When starting development of a feature, the target release
in which this feature will be incorporated may well be unknown. The
essence of a topic branch is that it exists as long as the feature
is in development, but will eventually be merged back into
**master** or a **release-\*** branch (to add the new feature or
bugfix to a next release) or discarded (in case of a disappointing
experiment).

Topic branches should exist in your **local** and **origin**
repositories only, there is no need for them to exist in
**doctrine**.

Working on topic branches
-------------------------

First create an appropriately named branch. When starting work on a
new topic, branch off from **doctrine/master** or a
**doctrine/release-\*** branch:

::

    $ git checkout -b DDC-588 doctrine/master
    Switched to a new branch "DDC-588"

Now do some work, make some changes then commit them:

::

    $ git status
    $ git commit <filespec>

Next, merge or rebase your commit against **doctrine/master**. With
your work done in a **local** topic branch, you'll want to assist
upstream merge by rebasing your commits. You can either do this
manually with ``fetch`` then ``rebase``, or use the
``pull --rebase`` shortcut. You may encounter merge conflicts,
which you should fix and then mark as fixed with ``add``, and then
continue rebasing with ``rebase --continue``. At any stage, you can
abort the rebase with ``rebase --abort`` unlike nasty merges which
will leave files strewn everywhere.

    **CAUTION** Please note that once you have pushed your branch
    remotely you MUST NOT rebase!


::

    $ git fetch doctrine
    $ git rebase doctrine/master DDC-588

or (uses tracking branch shortcuts):

::

    $ git pull --rebase

    **CAUTION** You must not rebase if you have pushed your branch to
    **origin**.


If you need to pull master into your branch after it has already
been pushed remotely, simply use:

::

    $ git pull

Push your branch to **origin**:

Finished topic branches should be pushed to **origin** for a
**collaborator** to review and pull into **doctrine** as
appropriate:

::

    $ git push origin DDC-588
    To git@github.com:hobodave/doctrine2.git
        * [new branch]      DDC-588 -> DDC-588</pre>

Now you are ready to send a pull request from this branch, and
update JIRA, to let a collaborator know your branch can be merged.

Topic Branch Cleanup
--------------------

Once your work has been merged by the branch maintainer, it will no
longer be necessary to keep the local branch or remote branch, so
you can remove them!

Sync your local master:

::

    $ git checkout master
    $ git pull --rebase

Remove your local branch using -d to ensure that it has been merged
by upstream. Branch -d will not delete a branch that is not an
ancestor of your current head.

From the git-branch man page:


.. raw:: html

   <pre>
   -d
       Delete a branch. The branch must be fully merged in HEAD.
   -D
       Delete a branch irrespective of its merged status.
   </pre>

Remove your local branch:

::

    $ git branch -d DDC-588

Remove your remote branch at **origin**:

::

    $ git push origin :DDC-588


The projects under the Doctrine umbrella use
`Phing <http://phing.info/trac>`_ to automate the process for
building our distributable PEAR packages.

Collaborator Workflow
=====================

Who is a collaborator?
----------------------

Collaborators are those who have been granted write access to the
main repository of a project. In the example of the ORM, it would
be this `repository <http://github.com/doctrine/doctrine2>`_. This
repository will be referred to as **doctrine** in this document.

You might want want to know how a collaborator is different from a
contributor. The **Collaborator Workflow** is used primarily for
the following:


-  Merging **contributor** branches into **doctrine/master** and/or
   **doctrine/release-\*** branches.
-  Creating @release-\*@ branches.
-  Tagging released versions within **master** and **release-\***
   branches.

Setup
-----

First you must Fork the
`repository <http://github.com/doctrine/doctrine2>`_ and clone your
fork locally:

::

    $ git clone git@github.com:username/doctrine2.git doctrine2-orm
    $ cd doctrine2-orm

Fetch dependencies using `composer <https://getcomposer.org/>`_:

::

    $ composer install

Now add the **doctrine** remote for collaborators:

::

    $ git remote add doctrine git@github.com:doctrine/doctrine2.git

Adjust your branch to track the doctrine master remote branch, by
default it'll track your origin remote's master:

::

    $ git config branch.master.remote doctrine

Optionally, add any additional contributor/collaborator forks,
e.g.:

::

    $ git remote add romanb git://github.com/romanb/doctrine2.git

Branching Model
---------------

Merging topic branches
~~~~~~~~~~~~~~~~~~~~~~


-  Topic branches **must** merge into **master** and/or any
   affected **release-\*** branches.
-  Merging a topic branch puts it into the *next* release, that is
   the next release created from **master** and/or the next patch
   release created from a specific **release-\*** branch.

Steps
^^^^^

Add remote repo for contributor/collaborator, if necessary (only
needs to be done once per collaborator):

::

    $ git remote add hobodave git://github.com/hobodave/doctrine2.git

Fetch remote:

::

    $ git fetch hobodave

Merge topic branch into master:

::

    $ git checkout master
    Switched to branch 'master'
    $ git merge --no-ff hobodave/DDC-588
    Updating ea1b82a..05e9557
    (Summary of changes)
    $ git push doctrine master

The **--no-ff** flag causes the merge to always create a new commit
object, even if the merge could be performed with a fast-forward.
This avoids losing information about the historical existence of a
topic branch and groups together all commits that together added
the topic.

Release branches
~~~~~~~~~~~~~~~~


-  May branch off from: **master**
-  Must merge back into: -
-  Branch naming convention: **release-\***

Release branches are created when **master** has reached the state
of the next major or minor release. They allow for continuous bug
fixes and patch releases of that particular release until the
release is no longer supported.

The key moment to branch off a new release branch from **master**
is when **master** reflects the desired state of the new release.

Creating a release branch
^^^^^^^^^^^^^^^^^^^^^^^^^

Release branches are created from the **master** branch. When the
state of **master** is ready for the upcoming target version we
branch off and give the release branch a name reflecting the target
version number. In addition the ".0" release is tagged on the new
release branch:

::

    $ git checkout -b release-2.0 doctrine/master
    Switched to a new branch "release-2.0"
    $ git push doctrine release-2.0
    $ git tag -a 2.0.0
    $ git push doctrine release-2.0

This new branch may exist for a while, at least until the release
is no longer supported. During that time, bug fixes are applied in
this branch (in addition to the **master** branch), if it is
affected by the same bug. Adding large new features here is
prohibited. They must be merged into **master**, and therefore,
wait for the next major or minor release.

Project Dependencies
====================

Project dependencies between Doctrine projects are handled through
composer. The code of the particular Doctrine project you
have cloned is located under **lib/Doctrine**. The source code of
dependencies to other projects resides under **vendor/**.

Bumping Versions
----------------

To bump/upgrade a dependency version you just need to update the
version constraint in composer.json and run:

::

    $ composer update


Running Tests
=============

Prerequisites
-------------


-  You must have installed the library with composer and the dev dependencies (default).

Tests
~~~~~

To run the tests :

::

    $ ./vendor/bin/phpunit
