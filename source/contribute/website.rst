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

Next run ``composer install`` to install all of the dependencies.

.. code-block:: console

    $ cd doctrine-website
    $ composer install

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

Build Documentation
-------------------

Now are you ready to start building the website! First, build the
documentation with the ``build-docs`` command.

This command will clone all the repositories for the documentation and
switch to the appropriate branches for each version of a project when
you pass the ``--sync-git`` option.

.. code-block:: console

    $ ./bin/console build-docs --sync-git

API Documentation
~~~~~~~~~~~~~~~~~

By default only the RST docs are built. You need to pass the ``--api``
option to also generate the API docs:

.. code-block:: console

    $ ./bin/console build-docs --sync-git --api

We use `Sami <https://github.com/FriendsOfPHP/Sami>`_ for generating the
PHP API documentation for each project.

Search Indexes
~~~~~~~~~~~~~~

To build the Algolia search indexes pass the ``--search`` option:

.. code-block:: console

    $ ./bin/console build-docs --sync-git --search

You will need to have the ``doctrine.website.algolia.admin_api_key``
parameter in ``config/local.yml`` in order to update the Algolia search
indexes.

Build the Website
-----------------

Now you are ready to build the website for the first time:

.. code-block:: console

    $ ./bin/console build-website

Go take a look at ``lcl.doctrine-project.org`` and the local website
should render. The built code for the website is written to
``/data/doctrine-website/build-dev``.

reStructuredText
----------------

The Doctrine documentation is written in a markup language called ``reStructuredText`` (RST). It is an easy-to-read, what-you-see-is-what-you-get plaintext markup syntax and parser system. The syntax is parsed by the `doctrine/rst-parser <https://www.doctrine-project.org/projects/rst-parser.html>`_ library.

You can see examples of RST `here <https://www.doctrine-project.org/rst-examples.html>`_.

Submitting Pull Requests
------------------------

If you see something that could be improved or a bug that needs fixing,
submit a pull request with the changes to
`doctrine/doctrine-website <https://github.com/doctrine/doctrine-website/>`_.

You can also take a look at the list of `open
issues <https://github.com/doctrine/doctrine-website/issues>`_ on GitHub
and look for something that needs help.
