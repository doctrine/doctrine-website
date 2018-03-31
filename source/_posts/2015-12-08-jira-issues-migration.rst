---
title: Jira Issues Migration
menuSlug: blog
authorName: 
authorEmail: 
categories: []
indexed: false
---
We have started the migration of all our Jira tickets to Github Issues.

These last months we had a lot of troubles with our Jira and we just cannot
find the time to update and maintain it anymore. On top of it, spam is causing
more maintenance for us deleting user accounts and tickets. Sadly ther seeems
to be no appropriate spam protection plugins and we couldn't prevent this.

We are by no means unsatisfied with Jira, and to be honest we have been
fighting this migration step as long as possible. Github Issues is a small fish
against Jira's powers, especially issue filtering, bulk operations and the
Agile board. But for Doctrine its best to migrate to Github to reduce our
maintenance and operations overhead and more tightly integrate with the tooling
we already have.

For now Common, DBAL and ORM issues have been imported into Github using the
`amazing Importer API <https://gist.github.com/jonmagic/5282384165e0f86ef105>`_.
Even though this API is still in Beta, it works quite flawlessly. If you are
interested in our migration scripts see `this repository in
Github <https://github.com/doctrine/jira-github-issues>`_. They are very
low-level and procedural but get the job done.

Jira has been changed into Read-Only mode for Common, DBAL and ORM projects,
please use the Github based issue trackers instead from now on:

- `ORM <https://github.com/doctrine/doctrine2/issues>`_
- `DBAL <https://github.com/doctrine/dbal/issues>`_
- `Common <https://github.com/doctrine/common/issues>`_

What is still missing?
----------------------

- Versions from Jira need to be exported and imported into Github releases with
  their release date, changelog and description.

- Permanent redirects for both Jira versions and issues to their respective
  Github counterparts have to be prepared and dynamically generated from our
  webserver, when we decommission Jira. This will help us keep deeplinks to
  Jira issues.

- Cleanup, categorize and prepare the newly imported Github issues.

We hope to complete this steps this week. The last one will take a bit longer.

What we could not import
------------------------

We were not able to import attachments, issue status transitions and
user/reporter assignments between Jira and Github. This information will be
lost once we disable Jira.
