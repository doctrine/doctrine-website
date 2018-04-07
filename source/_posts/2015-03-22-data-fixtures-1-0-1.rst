---
title: Doctrine Data Fixtures 1.0.1
menuSlug: blog
authorName: Sebastien Lavoie
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate availability Doctrine Data Fixtures ``1.0.1``.

In all `semver <http://semver.org/>`_ fashion, this is a bug fix release.

What is new in 1.0.x?
~~~~~~~~~~~~~~~~~~~~~

Please report any issues you may have with the update on 
`Github <https://github.com/doctrine/data-fixtures/issues>`_.

- Added Travis: `69c2230 <https://github.com/doctrine/data-fixtures/commit/69c2230dd15413cac013626729c30923632cf313>`_
- Now supports table quoting for dropping joined tables: `#180 <https://github.com/doctrine/data-fixtures/pull/180>`_
- Fixed ProxyReferenceRepository which was forcing to have a getId: `8ffac1c <https://github.com/doctrine/data-fixtures/commit/8ffac1c63f34124f693b93889fa32f4036eb241b>`_
- Fixed identifiers retrieval on ReferenceRepository if Entity is not yet managed my UnitOfWork: `dfc0dc9 <https://github.com/doctrine/data-fixtures/commit/dfc0dc9a3f6258c878768218fe49cc092ea8a8d1>`_
- Doctrine dependencies relaxed: `83a910f <https://github.com/doctrine/data-fixtures/commit/83a910f62b01715f3ed7317f5a4996417a698177>`_
- Fix purging non-public schema tables: `#171 <https://github.com/doctrine/data-fixtures/pull/171>`_

Release RoadMap
~~~~~~~~~~~~~~~

We expect to release following versions containing the pending patches in the next days:

 - ``1.1.0`` on ``2015-03-26``
 - ``1.2.0`` within ``2015-04``

Please note that these dates may change depending on the availability of our team.
