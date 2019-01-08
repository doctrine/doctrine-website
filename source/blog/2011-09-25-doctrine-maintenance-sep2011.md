---
title: "Maintenance 2.1 Releases for Common, DBAL and ORM and DBAL 2.0.9"
authorName: beberlei
authorEmail:
categories: [release]
permalink: /2011/09/25/doctrine-maintenance-sep2011.html
---
We have released the maintenance versions Common 2.1.2, DBAL 2.1.3 and
ORM 2.1.2.

-   [Common 2.1.2
    Changelog](https://www.doctrine-project.org/jira/browse/DCOM/fixforversion/10161)
-   [DBAL 2.1.3
    Changelog](https://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10162)
-   [ORM 2.1.2
    Changelog](https://www.doctrine-project.org/jira/browse/DDC/fixforversion/10154)

A total of 20 bugs have been fixed in all 3 components. The DBAL release
contains a security fix for the Oracle driver fixing [a possible SQL
injection issue](https://github.com/doctrine/dbal/issues/1321).
If you are using Oracle please update immediately. This security fix was
backported to 2.0 and a new 2.0.9 version was released.
