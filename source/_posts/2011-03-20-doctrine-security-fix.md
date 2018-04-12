---
title: Security Fix: Upgrade to 1.2.4 and 2.0.3 immediately
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: []
permalink: /2011/03/20/doctrine-security-fix.html
---
Because of a SQL injection possibility we urge users of Doctrine 1.2 and
2 to the newly released versions of both libraries immediately. Both
versions only include the security fix and no other changes to their
previous versions 1.2.3 and 2.0.2.

Affected versions are:

-   1.2.3 and earlier for PostgreSQL and DB2 Dialects
-   2.0.2 and earlier

The security hole was found today and affects the
`Doctrine\DBAL\Platforms\AbstractPlatform::modifyLimitQuery()` function
which does not cast input values for limit and offset to integer and
allows malicious SQL to be executed if these parameters are passed into
Doctrine 2 directly from request variables without previous cast to
integer. Functionality building on top using limit queries in the ORM
such as `Doctrine\ORM\Query::setFirstResult()` and
`Doctrine\ORM\Query::setMaxResults()` are also affected by this security
hole.

You can grab the packages from PEAR, Archive or Github, see the
respective links more details:

-   [ORM](http://www.doctrine-project.org/projects/orm/download)
-   [DBAL](http://www.doctrine-project.org/projects/dbal/download)

The fix for this security hole breaks backwards compatibility for
developers that extend the
`Doctrine\DBAL\Platforms\AbstractPlatform::modifyLimitQuery()` method,
because it is now marked as final. Please overwrite the
`Doctrine\DBAL\Platforms\AbstractPlatform::doModifyLimitQuery()` method
instead.
