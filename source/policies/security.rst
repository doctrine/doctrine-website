Security Policy
===============

This document explains how Doctrine security issues are handled by the core team and how
you should report a security issue if you think you have discovered one.

Reporting
--------------------------

If you think that you have found a security issue in Doctrine, please don't use the
issue tracker in GitHub and don't publish it publicly. Instead, all security issues
must be sent to `security@doctrine-project.org <mailto:security@doctrine-project.org>`_.
Emails sent to this address are forwarded to the Doctrine core team private mailing-list.

.. note::

    While we are working on a patch, please do not reveal the issue publicly. The resolution can take
    anywhere between a couple of days, a month or an indefinite amount of time depending on its complexity.

Resolving
---------

The core team will first try to confirm the vulnerability. When it is
confirmed, the core team works on a solution following these steps:

#. Send an acknowledgement to the reporter.
#. Work on a patch.
#. Get a CVE identifier from `mitre.org`_.
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
#. Publish the post on the Doctrine `blog`_.

.. _blog: https://www.doctrine-project.org/blog/
