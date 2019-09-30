---
title: "Doctrine MongoDB ODM 1.3.0 and 2.0.0-RC2 released"
authorName: Andreas Braun
authorEmail: alcaeus@doctrine-project.org
permalink: /2019/09/30/mongodb-odm-1.3.0-and-2.0.0-RC2.html
---

The Doctrine team is proud to announce that MongoDB ODM 1.3.0 and 2.0.0-RC2 have
been released. These releases are the culmination of a long effort to migrate
the ODM away from the legacy `mongo` extension to the new MongoDB driver
(`mongodb` extension and PHP library). This results in a number of BC breaks for
users, but will enable us to add many new features in future releases, among
them support for multi-document transactions.

MongoDB ODM 1.3.0 is a compatibility release targeted for users of the legacy
extension that want to migrate to MongoDB ODM 2.0. It helps identify BC breaks
by throwing deprecation notices and offering a forward compatibility layer where
possible. To efficiently find usages of deprecated code, you can use the PHPUnit
bridge developed by Symfony
([symfony/phpunit-bridge](https://github.com/symfony/phpunit-bridge)) which logs
all deprecation notices encountered during a run of PHPUnit. You can read more
about this component in the
[Symfony documentation](https://symfony.com/doc/current/components/phpunit_bridge.html).

MongoDB ODM 2.0.0RC-2 is the recommended package to use for those starting new
projects with MongoDB ODM. It ensures that you use the modern API for ODM
without having to worry about deprecations. While this is still a release
candidate, it is planned to make this version the next stable MongoDB ODM
release.

What’s new in MongoDB ODM 2.0?
==============================

Most importantly, this version no longer uses the legacy `mongo` extension. That
extension is no longer maintained and does not support server versions beyond
MongoDB 3.0. The new MongoDB driver ensures that MongoDB ODM can leverage
features and improvements contained in newer MongoDB versions, such as support
for multi-document transactions, retryable reads, retryable writes, change
streams, and much more.

Changing the driver also means significant changes to some APIs. Most
importantly, the GridFS API has been rewritten from scratch to conform with
MongoDB’s GridFS spec for drivers. If you’ve used GridFS before, this will be a
big change for you, but the new API is much simpler and cleaner to use. Check
out the
[GridFS documentation](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/storing-files-with-mongogridfs.html#storing-files-with-gridfs)
to find out how to use the new API. Unfortunately, we cannot provide a forward
compatibility layer for this, as re-implementing this API atop the legacy driver
is not feasible.

Lazy reference support has been changed completely and no longer uses proxy
objects from the deprecated doctrine/common library. Instead, it builds on
`ocramius/proxy-manager`, which gives us access to more advanced features like
partial proxy loading, which we will start leveraging in future releases.

In 2.0 we dropped support for the YAML mapping of documents. This step was
necessary to both reduce the complexity of the code base and lower the burden of
maintaining multiple mapping drivers. If you are currently using YAML mappings,
we provide a console command to migrate YAML mappings to the XML format. We are
currently working on an alternative that allows for a more flexible mapping
configuration system, but this is not ready yet and will only be provided in a
future 2.x release.

Migrating to MongoDB ODM 2.0
============================

If you are using MongoDB ODM 1.x, the upgrade consists of multiple steps. First,
ensure that you are fulfilling the necessary requisites for MongoDB ODM 2.0:
* PHP version 7.2 or newer
* ext-mongodb 1.5.0 or newer
* mongodb/mongodb library 1.4.0 or newer
* MongoDB 3.0 or newer

If you are already running PHP 7, you will most likely already be running
ext-mongodb as the legacy extension is not available for PHP 7. If you are still
running PHP 5.x, it is recommended that you migrate to PHP 7 before attempting
to use a newer ODM version. You can do so by following the instructions on
running ODM 1.x on PHP 7.

Once you fulfill all dependencies, the first step is updating to the latest 1.3
release of MongoDB ODM. If you are using Symfony, you also need to upgrade the
ODM bundle to its latest
[3.6 version](https://github.com/doctrine/DoctrineMongoDBBundle/releases/tag/3.6.0).
Once this is done, you can start fixing any deprecation notices that you find.
This should be a familiar process for any existing Symfony users. We tried to
provide compatibility layers where possible; unfortunately, we could not do so
in all cases.

The next step is upgrading to ODM 2.0 directly. For many users, this step
shouldn’t be a problem thanks to the compatibility layer in 1.x. There may be
some necessary changes depending on the features you use (e.g. GridFS).

What’s next for MongoDB ODM
===========================

During the past few years, we focussed our limited development time almost
exclusively on the driver migration, which came at the expense of supporting new
features in MongoDB. We plan to add support for many of those features in future
releases. You can get an overview of what’s planned by checking the roadmap. If
you are looking for a specific feature, please let us know in the issue tracker.

While not exhaustive or guaranteed, these are some of the features we plan to
implement in future releases:
* Support for multi-document transactions (on-demand and implicit while flushing
  the Document Manager)
* Support for new aggregation pipeline stages and operators
* Support for the $expr query operator
* Support for aggregation pipelines in update operations
* Support for reading documents from views instead of collections
* Atomic updates for collections using new array update operators

Support timeline
================

With these releases, we’re also introducing our new support timeline. Along with
the two releases announced above, we are also releasing the end-of-life release
for MongoDB ODM 1.2. We will not support MongoDB ODM 1.2 any more and encourage
users to upgrade to 1.3. Since 1.3 has no additional requirements over 1.2,
upgrading should be possible for all users of ODM 1.2.

MongoDB ODM 1.3 will be supported for at least 6 months after the first stable
release of ODM 2.0. We will communicate this date when releasing ODM 2.0. After
those 6 months, we will either drop support for ODM 1.3 or extend it for another
3 months, depending on the adoption rate of ODM 2.0. We are aware that the
number and kind of BC breaks for 2.0 pose a significant challenge for many
users, which is why we don’t want to force people to rush into this update.

During the support phase for MongoDB ODM 1.3, we will also continue to provide
bug fixes to the MongoDB Abstraction Layer that is used by MongoDB ODM 1.x. This
project will reach end-of-life at the same time as MongoDB ODM 1.3, and will no
longer be supported beyond that. We encourage users that depend on this library
to switch to using the MongoDB PHP Library, which is part of the official
MongoDB driver for PHP.

Contributing to MongoDB ODM
===========================

We are currently looking for contributors. This doesn’t necessarily mean
implementing new features or merging pull requests. Reporting or triaging
issues, requesting features, and reporting bugs are all extremely important and
helps us deliver better software!

Getting help
============

The documentation can be found on the website:
https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/index.html.
To get support, contact us via the #mongodb-odm channel within the Doctrine
Slack. If you believe you have found a bug, please file a bug report on GitHub.
