Policies
========

- `Deprecation Policy </policies/deprecation.html>`_
- `Security Policy </policies/security.html>`_
- `Release Policy </policies/releases.html>`_

Composer Lock File
------------------

All Doctrine projects must commit the ``composer.lock`` file. Tools like
`phpstan <https://github.com/phpstan/phpstan>`_ and `phpcs <https://github.com/squizlabs/PHP_CodeSniffer>`_
are quite fragile on patch releases and we don't want builds to start failing without us having made any changes
to our own code. Whenever a dependency needs to be upgraded, the ``composer.lock`` file
should be updated locally and the change submitted via pull request.
