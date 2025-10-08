---
permalink: /contribute/website/index.html
---

Contribute to Website
=====================

The source code for
`doctrine-project.org <https://www.doctrine-project.org>`_ is completely
open source and easy for you to setup locally so you can submit
contributions back to the project.

Installation
------------

First, create a fork of the
`repository <https://github.com/doctrine/doctrine-website>`_ and clone
it to a directory like ``/data``:

.. code-block:: console

    $ cd /data
    $ git clone git@github.com:username/doctrine-website.git

Next run ``composer install && yarn install`` to install all of the dependencies.

.. code-block:: console

    $ cd doctrine-website
    $ composer install && yarn install

Coding Standards
----------------

Copy the ``pre-commit`` hook to ``.git/hooks/pre-commit`` to ensure
coding standards are maintained:

.. code-block:: console

    $ cp pre-commit .git/hooks/pre-commit

Configuration
-------------

Copy the ``config/local.yml.dist`` config file that came with the
repository:

.. code-block:: console

    $ cp config/local.yml.dist config/local.yml

GitHub API
~~~~~~~~~~

In order to build the website, you will need to configure a GitHub API key
with the ``doctrine.website.github.http_token`` parameter in your ``config/local.yml`` file.
You can create an API token by going to the `Personal access tokens <https://github.com/settings/tokens>`_
section on the GitHub website.

Algolia Search Indexes
~~~~~~~~~~~~~~~~~~~~~~

In order to build the Algolia search indexes you will need to configure the
``doctrine.website.algolia.admin_api_key`` parameter in your ``config/local.yml`` file.
This key is not distribute to anyone, is optional and is not required in order to build
the website.

Edit your Hosts File
--------------------

Edit your ``/etc/hosts`` file and point ``lcl.doctrine-project.org`` at
your local web server. You will need to setup a virtual host in your web
server and point the root directory at
``/data/doctrine-website/build-dev``.

Build static website
--------------------

To build the full website and its documentation you need to run the command

.. code-block:: console

    $ ./bin/console --env=dev build

This will run several commands in the appropriate order to create the Doctrine website and its content.

If you want to build the website just for one project and project version, for example ORM in version 3.4, you can run the build command with
the following options:

.. code-block:: console

    $ ./bin/console --env=dev build --project=orm --libversion=3.4

The ``--project`` "orm" is the name of the repository name of the Doctrine project you want to build and ``--libversion``
is the version you would like to build. The version has to be in the ``major.minor`` format.

Search Indexes
~~~~~~~~~~~~~~

To build the Algolia search indexes pass the ``--search`` option:

.. code-block:: console

    $ ./bin/console --env=dev build --search

You will need to have the ``doctrine.website.algolia.admin_api_key``
parameter in ``config/local.yml`` in order to update the Algolia search
indexes.

Open the Doctrine website
~~~~~~~~~~~~~~~~~~~~~~~~~

Go take a look at ``lcl.doctrine-project.org`` and the local website
should render. The built code for the website is written to
``/data/doctrine-website/build-dev``.

Watch Frontend Assets
---------------------

After the initial build you can watch for frontend asset changes to update the stylesheets.

.. code-block:: console

    $ npm run watch

This process will run in the foreground and recompile the assets when a change is made to them. After refreshing the browser you should see the new assets loaded.

Run tests
---------

The Doctrine website includes Unit Tests and some Integration Tests to cover its functionality and to keep it stable.

JavaScript
~~~~~~~~~~

If some changes are provided for JavaScript then there have to be tests written in `Jest <https://jestjs.io>`. You'll
find the Jest tests in the ``jest`` directory of the Doctrine website project. The tests can be run with the following
command:

.. code-block:: console

    $ yarn jest

PHP
~~~

PHP tests are using `PHPUnit <https://phpunit.de>` to cover the website's PHP code. If you want to run tests for PHP, you have to
build the website with the ``test`` environment first.

.. code-block:: console

    $ ./bin/console --env=test build

**Why using a different environment for tests?** A full build of the website is essential for running integration tests
and the stability of the build. The Doctrine project has so many different projects with documentation, that it would take
too much time, locally or in GitHub Actions CI workflows, to finish a build. The ``test`` environment provides a minimal
configuration to improve runtime while covering all the use cases a website build has.

reStructuredText
----------------

The Doctrine documentation is written in a markup language called ``reStructuredText`` (RST). It is an easy-to-read, what-you-see-is-what-you-get plaintext markup syntax and parser system. The syntax is parsed by the `phpdocumentor <https://docs.phpdoc.org>`_ library.

You can see examples of RST `here <https://www.doctrine-project.org/rst-examples.html>`_.

Submitting Pull Requests
------------------------

If you see something that could be improved or a bug that needs fixing,
submit a pull request with the changes to
`doctrine/doctrine-website <https://github.com/doctrine/doctrine-website/>`_.

You can also take a look at the list of `open
issues <https://github.com/doctrine/doctrine-website/issues>`_ on GitHub
and look for something that needs help.
