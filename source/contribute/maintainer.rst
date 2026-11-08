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

Website metadata
----------------

A project repository can contain a ``.doctrine-project.json`` file at its
root. The file is optional. It lists the versions the website publishes and
provides explicit overrides for the values derived from git.

The ``versions`` list is the list of versions the website builds. Add one
entry per release line you want to publish. Patch, minor and major releases
inside an existing line do not require any change. A new ``X.Y`` line must be
added to the list.

Add the ``$schema`` key so your editor validates the file:

.. code-block:: json

    {
        "$schema": "https://www.doctrine-project.org/schema/website-schema.json",
        "name": "Object Relational Mapper",
        "shortName": "ORM",
        "versions": [
            {
                "name": "2.20",
                "lifeSupport": true
            },
            {
                "name": "3.7"
            },
            {
                "name": "3.8"
            },
            {
                "name": "4.0"
            }
        ]
    }

The other versions of the file are omitted from this excerpt.

Values that are not set are derived from git:

- ``branchName``: the branch guessed from the release tags. Set it explicitly
  only when git has no matching version.
- ``slug``: the highest version uses ``latest``, the other versions use their
  name.
- ``current``: the highest version with a stable release. A stable release is
  a tag without ``alpha``, ``beta``, ``rc`` or ``dev-``. Releases in the
  ``0.x`` series count as stable for this rule. The current version
  automatically gets the ``current`` and ``stable`` aliases.
- ``upcoming``: a version above ``current`` without a stable release. This
  covers the next minor and the next major branch, including repositories
  whose default branch is the next unreleased minor.
- ``maintained``: the current and upcoming versions. Set ``maintained`` to
  true to keep an older version supported during a migration period.

Set ``lifeSupport`` on an old major version that only receives security fixes.
It forces ``maintained`` to true and clears ``current`` and ``upcoming``.

Versions that are not in ``X.Y`` form, such as legacy ``master`` entries,
stay maintained.
