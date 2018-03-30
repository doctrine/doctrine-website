---
title: Doctrine Common 2.1.3, DBAL 2.1.5 and ORM 2.1.3 Releases
authorName: Benjamin Eberlei 
authorEmail: 
categories: [release]
indexed: false
---
The bugfix release is three weeks overdue, here is it now:

* `ORM 2.1.3 with 24 bugfixes and 1 security fix <http://www.doctrine-project.org/jira/browse/DDC/fixforversion/10164>`_
* `DBAL 2.1.5 with 6 bugfixes <http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10167>`_
* `Common 2.1.3 with 1 bugfix <http://www.doctrine-project.org/jira/browse/DCOM/fixforversion/10166>`_

The security fix concerns usage of the ASC/DESC orientation parameters in
``$repository->findBy($criteria, $orderBy)``, which is subject to SQL
injection when user-input is allowed into this method.

You can grab the downloads from the `project page <http://www.doctrine-project.org/projects>`_ ,
via `PEAR <http://pear.doctrine-project.org>`_ or `Git <https://github.com/doctrine>`_

Please update your installations.
