---
title: DoctrineModule 1.0.0 we have a stable release
menuSlug: blog
authorName: Gianluca Arbezzano
authorEmail: gianarb92@gmail.com
categories: []
permalink: /2016/02/02/doctrine_module_1_0_0_stable_release.html
---
We are happy to announce the first stable release for DoctrineModule!
[1.0.0](https://github.com/doctrine/DoctrineModule/releases/tag/1.0.0)
is ready to go after a couple of years of work.

The ["Initial
Commit"](https://github.com/doctrine/DoctrineModule/commit/13ededfcf10f9db6a4113cd9bdb4956ea145b6cd)
dates back to the date Oct 22, 2011 after 4 years, we are ready.

Thanks at all for yours contributions!

Update your composer configuration to use the stable version of this
project.

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/doctrine-module": "~1.0"
    }
}
~~~~

Changes since 0.10.0
====================

This is a list of issues resolved in `1.0.0` since `0.10.0`:

-   [[\#523]](https://github.com/doctrine/DoctrineModule/pull/523)
    Remove deprecated api call from test
-   [[\#547]](https://github.com/doctrine/DoctrineModule/pull/547) Allow
    for the use of ZendCacheServiceStorageCacheAbstractServiceFactory

Please report any issues you may have with the update on the mailing
list or on [GitHub](https://github.com/doctrine/DoctrineModule/issues).

Remember to read [our
documentation](https://github.com/doctrine/DoctrineModule/tree/master/docs)
and improve it with your knowledge.
