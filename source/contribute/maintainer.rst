---
layout: default
title: Maintainer Workflow
menuSlug: contribute
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

Automation
----------

Each of the following steps are documented for humans to perform them
manually, but maintainers and contributors are highly encouraged to try
and automate them away into tooling that is commonly available across
the organisation projects, as well as the community at large.

Setup
-----

First you must Fork the
`repository <http://github.com/doctrine/doctrine2>`_ and clone your fork
locally:

.. code-block:: console

    $ git clone git@github.com:<username>/doctrine2.git doctrine2-orm
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

You can also fetch all open pull requests via `git fetch` if you
edit your ``.git/config`` as following:

... code-block:: console

    [remote "doctrine"]
        url = git@github.com:doctrine/doctrine2.git
        fetch = +refs/heads/*:refs/remotes/doctrine/*
        # add this:
        fetch = +refs/pull/*/head:refs/remotes/doctrine/pr/*

Distinguishing features, bugs and critical fixes
------------------------------------------------

The primary role of a maintainer, besides being also a contributor,
is to sort incoming proposals by their category:

-  **new features** are additions that provide new API or new behavior
   that was previously not exposed by the project
-  **improvements** are additions that improve existing API by making
   it more clear, by improving the usability and performance, or by
   verifying existing behavior via refactoring, new tests or static
   analysis.
-  **bug fixes** are changes to the codebase that do correct invalid
   behaviour.
-  **critical fixes** are changes to the codebase that correct invalid
   behavior that was erroneously introduced, and prevents installation
   or usage of the library by a very large portion of the community.
-  **security fixes** are changes to the codebase that correct existing
   behavior in the codebase that may lead to substantial financial or
   personal damage to consumers of the packages due to malicious
   attack vectors.
-  **deprecations** are changes to the codebase that do mark existing
   API as "to be removed in future"
-  **backwards-compatibility breakages** (or **BC breaks** in short)
   are modifications to the existing API or implementation that would
   result in downstream users having to correct their software to
   adapt to the new changes. Maintainers should also prevent any
   unnecessary BC breaks, and always evaluating if it is worth
   introducing them.

Stability and Semantic Versioning
---------------------------------

A maintainer must also always consider that any proposal, regardless
how well tested and verified it could be, brings in some instability.
In order to reduce the amount of defects and/or regressions reaching
downstream users, a maintainer must therefore always consider
carefully where a patch may land.

Packages controlled by the Doctrine organisation are to follow
`Semantic Versioning (SemVer) <https://semver.org/spec/v2.0.0.html>`_
rules, with the famous ``MAJOR.MINOR.PATCH`` naming scheme.

This effectively means:

- **PATCH** only contains **bug fixes**, **security fixes** and
  **critical fixes**
- **MINOR** can contain everything that is in **PATCH** plus
  **new features**, **improvements** and **deprecations**
- **MAJOR** can contain all of the above plus **BC breaks**

Whilst it is possible to automate some of these decisions, humans
are still better at categorising these changes due to the amount of
nuances that are involved in the software development process.

Branching Model
---------------

In order to maintain all the stability invariants that SemVer imposes,
it is vital that maintainers know where to merge incoming patches.

Packages in the doctrine organisation should use following branching
structure:

 * ``develop`` - extremely unstable, points at the next planned
   **MAJOR** release, may be rebased in order to speed up individual
   maintainers prototyping new changes. Changes on ``develop`` can
   be radical, and should not be relied upon.
 * ``master`` - always to be considered as the next planned **MAJOR**
   or **MINOR** release (depending on team internal agreement).
   Consumers should not rely on ``master`` unless they are prepared
   to adapt their codebase at every potentially breaking change.
 * ``MAJOR.MINOR.x`` - always to be considered the next planned
   **PATCH** release. Maintainers should keep these (multiple) branches
   stable. The base of these branches MUST be the ``MAJOR.MINOR.0`` tag.
   Without a pre-existing tag, these branches should not exist

Releasing packages
------------------

**MAJOR.0.0** and **MAJOR.MINOR.0** releases **MUST** be tagged from
``master``.

When tagging a new **MAJOR.0.0** or **MAJOR.MINOR.0** release, a
corresponding **MAJOR.MINOR.x** branch should be branched off the tag.

**MAJOR.MINOR.1+** releases must be tagged from the corresponding
``MAJOR.MINOR.x`` branch.

This effectively means that a typical doctrine package should have a
git graph like following:

.. code-block:: console

                                             ----- develop
                                            /
    1.0.0 ----- 1.1.0 ----- 2.0.0 ------ master
      |           |           \
      |           |            ----- 2.0.x
      |           \
      |            ----- 1.1.1 ----- 1.1.2 ----- 1.1.x
      \
       ----- 1.0.1 ----- 1.0.2 ----- 1.0.x

Preparing a release
-------------------

Assuming that all tasks for a planned release are completed, a
maintainer would then be in the position of preparing a git tag,
which for doctrine project also corresponds to a release.

To do that:

-  ensure that all known introduced **BC Breaks** are documented
   in ``UPGRADE.md``.
-  ensure that the automated tests for the branch from which
   a release has to be tagged are passing.
-  prepare a release description, which should:
    -  list all patches
    -  describe the points of major relevance in the patch
   maintainers may want to use a tool such
   as `weierophinney/changelog_generator <https://github.com/weierophinney/changelog_generator>`_
   or `jwage/changelog-generator <https://github.com/jwage/changelog-generator>`_)
   in order to generate such release notes

Then it is possible to tag a release.

Please note that tags *MUST* be signed. Unsigned releases will be
removed and replaced.

For a new patch release,
this is the workflow (here with **MAJOR** = 5, **MINOR** = 3 and **PATCH** = 1):

.. code-block:: console

    $ git checkout 5.3.x
    $ git pull --ff-only
    $ git tag -s 5.3.1 -F my-release-notes.txt --cleanup=verbatim
    $ git push origin 5.3.1

To release a new minor or major version, the workflow starts from
``master``:

.. code-block:: console

    $ git checkout master
    $ git pull --ff-only
    $ git tag -s 6.2.0 -F my-release-notes.txt --cleanup=verbatim
    $ git checkout -b 6.2.x
    $ git push origin 6.2.0 6.2.x

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
