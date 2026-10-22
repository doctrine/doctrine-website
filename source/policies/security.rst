Security Policy
===============

This document explains how Doctrine security issues are handled by the core team and how
you should report a security issue if you think you have discovered one.

Reporting
---------

If you think that you have found a security issue in Doctrine, please don't use the
public issue tracker in GitHub and don't publish it publicly.

**Preferred: GitHub private vulnerability reporting.** Open the "Security" tab of the
affected repository and choose "Report a vulnerability". Only the maintainers of that
repository can see the report.

**Fallback: email.** If you cannot use GitHub, or the repository does not offer private
reporting, send your report to
`security@doctrine-project.org <mailto:security@doctrine-project.org>`_.
Emails sent to this address are forwarded to the Doctrine core team private mailing-list.

If you are unsure whether something is a security issue, please report it privately.
We would rather triage a regular bug than have a vulnerability discussed in public.

.. note::

    While we are working on a patch, please do not reveal the issue publicly. The resolution can take
    anywhere between a couple of days, a month or an indefinite amount of time depending on its complexity.

What is a security issue?
-------------------------

A flaw in a Doctrine library that allows an attacker to compromise the confidentiality,
integrity or availability of an application that uses the library as documented. Examples
are injection, execution of code or instantiation of classes based on untrusted data,
unintended access to or modification of data, and silently weakened protections such as
encryption.

The following are usually not security issues: bugs without security impact (use the
issue tracker), misuse of APIs that are documented to accept raw input, vulnerabilities
in third-party dependencies or the database server (please report those to the respective
projects), and insecure deployments.

Each project may list examples specific to it in its ``SECURITY.md``.

Reported publicly by mistake
----------------------------

If a possible vulnerability was reported in a public issue or pull request, maintainers may
edit, hide or lock it and continue in private. Please file a private report referencing it.

Resolving
---------

The core team will first try to confirm the vulnerability. When it is
confirmed, the core team works on a solution following these steps:

#. Send an acknowledgement to the reporter.
#. Work on a patch, in a private fork if the report was made through GitHub.
#. Request a CVE identifier, preferably through the GitHub security advisory.
#. Write a security announcement for the Doctrine `blog`_ about the
   vulnerability. This post should contain the following information:

   * a title that always include the "Security release" string.
   * a description of the vulnerability.
   * the affected versions.
   * the possible exploits.
   * how to patch/upgrade/workaround affected applications.
   * the CVE identifier.
   * credits.

#. Send the patch and the announcement to the reporter for review.
#. Apply the patch to all maintained versions of Doctrine.
#. Release new versions for all affected versions.
#. Publish the GitHub security advisory and the post on the Doctrine `blog`_.

.. _blog: https://www.doctrine-project.org/blog/
