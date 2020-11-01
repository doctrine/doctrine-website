Policies
========

- `Deprecation Policy </policies/deprecation.html>`_
- `Security Policy </policies/security.html>`_
- `Release Policy </policies/releases.html>`_

Tools and versioning constraints
--------------------------------

All Doctrine projects should use tight constraints for tools like
`phpstan <https://github.com/phpstan/phpstan>`_ and `phpcs
<https://github.com/squizlabs/PHP_CodeSniffer>`_. Those are quite
fragile on patch releases and we don't want builds to start failing
without us having made any changes to our own code.
