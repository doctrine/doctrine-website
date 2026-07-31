---
permalink: /contribute/maintainer/index.html
---

Maintainer Workflow
===================

Who is a maintainer? Maintainers are those who have been granted write
access to the main repository of a project. In the example of the ORM,
it would be this `repository <https://github.com/doctrine/orm>`_.
This repository will be referred to as **doctrine** in this document.

You might want to know how a maintainer is different from a contributor.
The **Maintainer Workflow** is used primarily for the following:

-  Merging **contributor** branches into release branches.
-  Creating release branches.
-  Tagging released versions within release branches.

Branching Model
---------------

Merging topic branches:

- Topic branches **must** merge into the lowest supported branch when
  they are a bugfix, or an improvement that does not affect stability
  (documentation, CI pipeline, tests).
- Topic branches **must** merge into the next minor release if they
  are a new feature that does not involve a backwards-compatibility
  break, or a deprecation.
- Topic branches **must** merge into the next major release if they
  contain a backwards-compatibility break. Reviewers and contributors
  should try hard to think of a way to make it backwards compatible and
  contribute it to the next minor instead. Ideally, the next major
  release should be about removing deprecated features.

Release Branches
----------------

- May branch off from the next minor or major branch.
- Branch naming convention: 1.0.x, 2.0.x, 2.1.x

Release branches are created either manually or through
``laminas/automatic-releases`` (depending on the configuration). In any
case, this should be done right after a release is tagged.

Creating a release branch
^^^^^^^^^^^^^^^^^^^^^^^^^

Release branches are created from the last major or minor tag that was
created, right after it is created. In repositories that do not enable
the ``use-next-minor-as-default-branch`` switch for the
``laminas/automatic-releases`` workflow, this needs to be done manually

.. code-block:: console

    $ git switch --create 2.0.x 2.0.0
    Switched to a new branch "2.0.x"
    $ git push doctrine 2.0.x

Tagging a release
-----------------

We use ``laminas/automatic-releases`` to make releasing as easy as
closing a milestone. Before closing a milestone, one should check that
it is complete. If not, they should unassign issues and pull requests,
or close them / merge them as appropriate. It is also a good idea to
check which pull requests were merged since the last release on the
relevant branch, and to make sure each one is properly labelled and
assigned to the milestone.

Labels are used to generate the release notes, and we should strive to
provide a consistent experience accross repositories. Here are labels
that we recommend adding on every repository:

BC Break
  Changes or removals that may require changes or actions from the user
  before the package can be used again.

Improvement
  Additions to the feature set or API of the package that are backward
  compatible

Bugfix
  Changes that fix an issue affecting the user of the package.

Documentation
  Includes changes to rst files, markdown files as well as code
  comments.

Deprecation
  For pull requests that *introduce* a new deprecation (as opposed to
  addressing deprecations from another package, or removing deprecated
  code paths)

Internal
  Changes that do not impact the end user, but might impact contributors
  or maintainers, such as improvements to the CI, bugfixes for the test
  suite, etc.
