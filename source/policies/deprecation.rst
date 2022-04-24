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

Using ``doctrine/deprecations``
-------------------------------

In some cases, you may need to conditionally deprecate functionality. In
these cases, it is required to use the `Doctrine deprecations`_ library.


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

.. _Doctrine deprecations: https://github.com/doctrine/deprecations
.. _Psalm: https://github.com/vimeo/psalm
.. _PHPStan: https://github.com/phpstan/phpstan
.. _PHP Parser: https://github.com/nikic/php-parser
