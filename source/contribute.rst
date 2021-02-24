---
permalink: /contribute/index.html
---

Contribute
==========

Welcome to the Doctrine Project Contributors Guide. This documentation
aims to document how contributors and maintainers should work when using
git, development workflow, build process, dependency management, etc.

About
-----

The Doctrine Project is the home of a selected set of PHP libraries
primarily focused on providing persistence services and related
functionality. Its prize projects are the Object Relational Mapper and
the Database Abstraction Layer it is built on top of. You can view a
list of all `projects </projects.html>`_ on the website.

Contributors vs Maintainers
---------------------------

Before continuing you need to understand the difference between a
contributor and a maintainer.

-  Contributor: A contributor is someone from the outside not on the
   core development team of the project that wants to contribute some
   changes to a project.
-  Maintainer: A maintainer is someone on the core development team of
   the project and has commit access to the main repository of the
   project.

Contributor Workflow
--------------------

Who is a contributor? A contributor can be anyone! It could be you.
Continue reading this section if you wish to get involved and start
contributing back to a Doctrine project.

Initial Setup
-------------

-  Setup a `GitHub <https://github.com>`_ account.
-  Fork the repository of the project you want to contribute to. In this example
   it will be `DBAL <https://github.com/doctrine/dbal>`_
-  Clone your fork locally

.. code-block:: console

    $ git clone git@github.com:username/dbal.git

-  Enter the dbal directory and add the **doctrine** remote

.. code-block:: console

    $ cd dbal
    $ git remote add doctrine git://github.com/doctrine/dbal.git

Branching from the default branch
---------------------------------

New pull requests are created with the repository's default branch as base branch.
The default branch is the branch you see when you enter the repository page on GitHub.

.. image:: ../images/default-branch.png
   :alt: The default branch
   :style: margin-bottom: 20px

In this DBAL example, it's the branch with the name **2.11.x**. The branch name reflects the
current lowest supported version of a repository.

Newly introduced changes to 2.11.x will be up-merged at a later point in time
to newer version branches (e.g. 2.12.x, 3.0.x). This way you don't have to
re-introduce a new fix or feature of 2.11.x with another pull request to
the other version branches.

Keeping the default branch up-to-date!
--------------------------------------

Once all this is done, you'll be able to keep your local branches up to
date with the following command:

.. code-block:: console

    $ git fetch doctrine

Branching Model
---------------

The following names will be used to differentiate between the different
repositories:

-  **doctrine** - The "official" Doctrine DBAL repository
-  **origin** - Your fork of the official repository on GitHub
-  **local** - This will be your local clone of **origin**

As a **contributor** you will push your completed **local** topic branch
to **origin**. As a **contributor** you will pull updates from
**doctrine**. As a **maintainer** (write-access) you will merge branches
from contributors into **doctrine**.

Primary Branches
----------------

The **doctrine** repository holds the following primary branches:

-  **doctrine/2.11.x** Development towards the next release.
-  **doctrine/\*** Maintenance branches of existing releases.

These branches exist in parallel and are defined as follows:

**doctrine/2.11.x** is the branch where the source code of **HEAD**
always reflects the latest version. Each released stable version will be
a tagged commit in a **doctrine/\*** branch. Each released unstable
version will be a tagged commit in the **doctrine/2.11.x** branch.

    **NOTE** You should never commit to your forked default branch (**origin/2.11.x**).
    Changes to **origin/2.11.x** will never be merged into
    **doctrine/2.11.x**. All work must be done in a **topic branch**,
    which are explained below.

Topic Branches
--------------

Topic branches are for contributors to develop bug fixes, new features,
etc. so that they can be easily merged to **2.11.x**. They must follow a
few rules as listed below:

-  May branch off from: **2.11.x** whenever possible, or a newer version
   branch otherwise. Keep in mind that your changes will be
   up-merged to higher version branches by maintainers after the merge if
   they are applicable.
-  Branch naming convention: anything except master, the default branch name,
   or version branch names.

Topic branches are used to develop new features and fix reported issues.
When starting development of a feature, the target release in which this
feature will be incorporated may well be unknown. The essence of a topic
branch is that it exists as long as the feature is in development, but
will eventually be merged into **2.11.x** or a release branch (to
add the new feature or bugfix to a next release) or discarded (in case
of a disappointing experiment).

Topic branches should exist in your **local** and **origin**
repositories only, there is no need for them to exist in **doctrine**.

Creating a topic branch
-----------------------

First create an appropriately named branch. When starting work on a new
topic, branch off from **doctrine/2.11.x** or a **doctrine/\*** branch:

.. code-block:: console

    $ git checkout -b fix-weird-bug doctrine/2.11.x
    Switched to a new branch "fix-weird-bug"

Now do some work, make some changes then commit them:

.. code-block:: console

    $ git status
    $ git add -p
    $ git commit -v

Crafting meaningful commit messages
-----------------------------------

Commit messages should look like emails, meaning they should have a
subject, but also a body. The subject should be on the first line, and
not exceed 50 chars. It should tell us what you did, and every change in
the diff should have to do with that subject. The body should be
separated from it by a blank line and should tell us *why* you did what
you did. That is also a good place to tell people about alternate
solutions that were considered and the reasons for rejecting them. Links
to related issues are more than welcome, but should be summarized so
that the pull request can be understood without resorting to them.
Ideally, the git history should be understandable without a network
connection. Here is an example of a good although fictitious commit
message::

    Call foo::bar() instead of bar::baz()

    This fixes a bug that arises when doing this or that, because baz()
    needs a flux capacitor object that might not be defined.
    I considered calling foobar(), but decided against because
    $nonObviousYetVeryGoodReason
    Fixes #42

There are already a few articles (or even single purpose websites) about
this in case you want to read more about this:

- `Deliberate git <https://www.rakeroutes.com/deliberate-git>`_
- `Commit message style for git <https://commit.style/>`_
- `A note about git commit messages <https://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html>`_

Rebasing on upstream changes
----------------------------

Next, merge or rebase your commit against **doctrine/2.11.x**. With your
work done in a **local** topic branch, you'll want to assist upstream
merge by rebasing your commits. You can either do this manually with
``fetch`` then ``rebase``, or use the ``pull --rebase`` shortcut. You
may encounter merge conflicts, which you should fix and then mark as
fixed with ``add``, and then continue rebasing with
``rebase --continue``. At any stage, you can abort the rebase with
``rebase --abort`` unlike nasty merges which will leave files strewn
everywhere.

.. code-block:: console

    $ git fetch doctrine
    $ git rebase doctrine/2.11.x fix-weird-bug

Push your branch to **origin**:

Finished topic branches should be pushed to **origin** for a
**maintainer** to review and pull into **doctrine** as appropriate:

.. code-block:: console

    $ git push origin fix-weird-bug
    To git@github.com:hobodave/dbal.git
        * [new branch]      fix-weird-bug -> fix-weird-bug

Now you are ready to send a pull request from this branch and ask for a
review from a maintainer.

Topic Branch Cleanup
--------------------

Once your work has been merged by the branch maintainer, it will no
longer be necessary to keep the local branch or remote branch, so you
can remove them!

Sync your local 2.11.x branch:

.. code-block:: console

    $ git checkout 2.11.x
    $ git pull --rebase

Remove your local topic branch using -d to ensure that it has been merged by
upstream. Branch -d will not delete a branch that is not an ancestor of
your current head.

From the git-branch man page:

.. code-block:: console

    -d
        Delete a branch. The branch must be fully merged in HEAD.
    -D
        Delete a branch irrespective of its merged status.

Remove your local topic branch:

.. code-block:: console

    $ git branch -d fix-weird-bug

Remove your remote branch at **origin**:

.. code-block:: console

    $ git push origin :fix-weird-bug

Project Dependencies
--------------------

Project dependencies between Doctrine projects are handled through
composer. The code of the particular Doctrine project you have cloned is
located under **lib/Doctrine**. The source code of dependencies to other
projects resides under **vendor/**.

To bump/upgrade a dependency version you just need to update the version
constraint in composer.json and run:

.. code-block:: console

    $ composer update

Running Tests
-------------

You must have installed the library with composer and the dev
dependencies (default). To run the tests:

.. code-block:: console

    $ ./vendor/bin/phpunit

Security Disclosures
--------------------

You can read more about how to report security issues in our `Security Policy <https://www.doctrine-project.org/policies/security.html>`_.

Maintainer Workflow
-------------------

You can learn more about the maintainer workflow
`here </maintainer/>`_. Continue reading if you are
interested in learning more about how to get started with your first
contribution.

Website
-------

The `doctrine-project.org <https://www.doctrine-project.org/>`_ website
is completely open source! If you want to learn how to contribute to the
Doctrine website and documentation you can read more about it
`here </website/>`_.
