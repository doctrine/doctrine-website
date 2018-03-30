---
title: Doctrine MongoDB ODM release 1.0.4
authorName: Andreas Braun
authorEmail: alcaeus@alcaeus.org
categories: []
indexed: false
---
We are happy to announce the immediate availability of Doctrine MongoDB ODM
`1.0.4 <https://github.com/doctrine/mongodb-odm/releases/tag/1.0.4>`__.

Bug fixes in this release
-------------------------

Notable fixes may be found in the
`changelog <https://github.com/doctrine/mongodb-odm/blob/master/CHANGELOG-1.0.md#104-2015-12-15>`__.
A full list of issues and pull requests included in this release may be found
in the
`1.0.4 milestone <https://github.com/doctrine/mongodb-odm/issues?q=milestone%3A1.0.4>`__.

Installation
------------

You can install the latest version using the following ``composer.json`` definitions:

.. code-block:: json

  {
      "require": {
          "doctrine/mongodb-odm": "^1.0.4"
      }
  }

Doctrine MongoDB ODM 1.1 requires PHP 5.5+
------------------------------------------

The current ``master`` branch saw its PHP requirement bumped to 5.5 recently. If
you are still using the master version in your project you should switch to a
stable release as soon as possible:

.. code-block:: json

  {
      "require": {
          "doctrine/mongodb-odm": "^1.0"
      }
  }

This will ensure you are using stable versions and will use 1.1 as soon as it's
released.

The upcoming releases of Doctrine MongoDB (1.3) and ODM (1.1) will also drop
support for all MongoDB driver versions before 1.5. If you are still using an
older driver, please consider upgrading it in order to receive future updates.
