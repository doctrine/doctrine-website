---
layout: default
title: Deprecation Policy
menuSlug: development
permalink: /policies/deprecation.html
---

Deprecation Policy
==================

The Doctrine team will adhere to this deprecation policy whenever it is reasonably possible.

Using ``@deprecated`` and ``PHPStan Deprecation Rules``
-------------------------------------------------------

The primary and preferred method for deprecating functionality within Doctrine is by using
the ``@deprecated`` annotation. To have usages of functionality in your dependencies analyzed
and reported, you must use a tool like `PHPStan Deprecation Rules`_ or `Psalm Static Analysis`_.

Here is an example of how this would be used in Doctrine:

.. code-block:: php

    namespace Doctrine\ORM;

    class EntityManager
    {
        // ...

        /**
         * @deprecated
         */
        public function getSomething() : int
        {
            // ...
        }
    }

Then, if your code directly depends on ``doctrine/orm`` which provides the
``Doctrine\ORM\EntityManager`` class, you will get a warning about it when you run
`PHPStan Deprecation Rules`_ or `Psalm Static Analysis`_. Here is an example:

.. code-block:: php

    namespace App;

    use App\Entities\User;
    use Doctrine\ORM\EntityManager;

    class UserRegistration
    {
        /** @var EntityManager */
        private $entityManager;

        // ...

        public function create(array $userData) : User
        {
            $something = $this->entityManager->getSomething();

            // ...
        }
    }

Using ``trigger_error()`` and ``phpunit-bridge``
------------------------------------------------

In some cases, you may need to conditionally deprecate functionality. In these cases, it is required
to use the ``trigger_error()`` function and a tool like `PHPUnit Bridge`_ which implements a custom
error handler for you to use to collect and report these warnings. **This method is not recommended
and Doctrine will try to avoid using it if possible.**

If you do not wish to use `PHPUnit Bridge`_ you can implement your own custom error handler using
the `set_error_handler`_ function.

.. note::

    In order for this strategy to work, you have to configure your `error_reporting`_ properly
    in both your development and production environments. You don't want errors of type ``E_USER_DEPRECATED``
    being reported from PHP.

Here is an example of how this would be used in Doctrine:

.. code-block:: php

    namespace Doctrine\ORM;

    class EntityManager
    {
        // ...

        public function getAnotherThing() : int
        {
            if ($this->featureFlag) {
                trigger_error('This feature has been disabled.', E_USER_DEPRECATED);
            }

            // ...
        }
    }

Now in order to get those warnings reported to you in your automation, you need to use a tool like `PHPUnit Bridge`_.

Tools
-----

There are a few tools out there that you can use to integrate deprecation warnings into your
automation.

PHPStan Deprecation Rules
~~~~~~~~~~~~~~~~~~~~~~~~~

Doctrine uses and recommends PHPStan_ for reporting usages of deprecated functionality in your code.

.. code-block:: console

    $ composer require --dev phpstan/phpstan
    $ composer require --dev phpstan/phpstan-deprecation-rules

You will need to setup a configuration file in the root of project named ``phpstan.neon.dist``

.. code-block::

    includes:
        - vendor/phpstan/phpstan-phpunit/extension.neon
        - vendor/phpstan/phpstan-strict-rules/rules.neon

    parameters:
        level: 0
        paths:
          - lib
          - tests

Now you can run the ``vendor/bin/phpstan`` command:

.. code-block:: console

    $ ./vendor/bin/phpstan analyze

Psalm Static Analysis
~~~~~~~~~~~~~~~~~~~~~

Psalm_ is a static analysis tool for finding errors in PHP applications, built on top of `PHP Parser`_.

It's able to find a large number of issues, but it can also be configured to only care about a small subset of those.

.. code-block:: console

    $ composer require vimeo/psalm

PHPUnit Bridge
~~~~~~~~~~~~~~

The `PHPUnit Bridge`_ provides utilities to report legacy tests and usage of deprecated code.

.. code-block:: console

    $ composer require --dev "symfony/phpunit-bridge:*"

.. _Psalm: https://github.com/vimeo/psalm
.. _PHPStan: https://github.com/phpstan/phpstan
.. _PHP Parser: https://github.com/nikic/php-parser
.. _PHPUnit Bridge: https://github.com/symfony/phpunit-bridge
.. _error_reporting: http://php.net/manual/en/function.error-reporting.php
.. _set_error_handler: http://php.net/manual/en/function.set-error-handler.php
