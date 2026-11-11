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

Publishing a release
--------------------

We use ``laminas/automatic-releases`` to make releasing as easy as
closing a milestone. Before closing a milestone, one should check that
it is complete and contains only merged pull requests, as opposed to
issues or closed pull requests.

There might also be open pull requests that are ready to be merged and
could be included in the release.

It is also a good idea to check which pull requests were merged since
the last release on the relevant branch, and to make sure each one is
properly labelled, assigned to the milestone, and has a title that is a
correct description of the change. A good way to do the first two checks
is to filter the list of pull requests using the following filter:
`state:merged (no:label OR no:milestone)` in the GitHub UI.

Once the milestone is closed, the Github Action should run. It might
fail in case there is nothing to merge up, but it should create a new
release.

You might then want to rework the release notes, using a more sensible
ordering showing the most user-facing changes first, and making e.g.
"Bugfix" plural.

Publishing a patch release
^^^^^^^^^^^^^^^^^^^^^^^^^^

Some repositories have 2 patch branches:

- a legacy or life support patch branch;
- a current patch branch.

In this case, merge the legacy branch into the current patch branch
first, so that any conflicts are resolved before releasing anything.

Then publish the legacy patch branch, followed by the current patch
branch. Publish both within a short time frame, so that projects
upgrading from the legacy branch to the current one do not hit a
regression. Once the release notes are generated, include a link to the
legacy release notes in the current release notes.

Publishing a minor release
^^^^^^^^^^^^^^^^^^^^^^^^^^

Before publishing a minor release, make sure no patch release is worth
publishing on a lower branch, and that the previous minor branch is
fully merged up into the next minor branch.

Once the minor release is published, change the default branch of the
repository on GitHub to the next minor branch. Open a pull request to
update ``.doctrine-project.json`` and, in some projects, other files
such as ``README.md`` or, in the case of Symfony bundles,
``.symfony.bundle.yaml``.

Then create a new release branch for the next minor release,
retarget open pull requests targeting the former minor branch to the new
minor branch, and retarget open pull requests targeting the former patch
branch to the new patch branch.

And finally delete the former current branch, once the pull request is
merged as well as GitHub milestones corresponding to it that might
remain.
