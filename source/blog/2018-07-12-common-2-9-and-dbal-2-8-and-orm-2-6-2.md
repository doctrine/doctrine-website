---
title: "Phasing out Doctrine Common & release of DBAL 2.8 and ORM 2.6.2"
menuSlug: blog
layout: blog-post
authorName: Michael Moravec
authorEmail: doctrine@majkl.me
permalink: /2018/07/12/common-2-9-and-dbal-2-8-and-orm-2-6-2.html
---

### Common 2.9 and phasing out the package

As another step in the ongoing effort to eliminate `doctrine/common`,
there are now three new separate Doctrine packages:

 * [`doctrine/persistence`](https://github.com/doctrine/persistence)
 * [`doctrine/event-manager`](https://github.com/doctrine/event-manager)
 * [`doctrine/reflection`](https://github.com/doctrine/reflection)

This release introduces the following deprecations:

 * `Doctrine\Common\Proxy` component is deprecated, use
   [`ocramius/proxy-manager`](https://github.com/ocramius/ProxyManager) instead;
 * `Doctrine\Common\Util\Debug` is deprecated, use
   [`symfony/var-dumper`](https://github.com/symfony/var-dumper) instead;
 * `Doctrine\Common\Lexer` is deprecated, use `Doctrine\Common\Lexer\AbstractLexer`
   from [`doctrine/lexer`](https://github.com/doctrine/lexer) or migrate to
   [hoa/compiler](https://github.com/hoaproject/Compiler) instead;
 * `Doctrine\Common\Util\Inflector` is deprecated, use `Doctrine\Common\Inflector\Inflector`
   from [`doctrine/inflector`](https://github.com/doctrine/inflector) instead;
 * `Doctrine\Common\Util\ClassUtils` is deprecated without replacement;
 * `Doctrine\Common\Version` is deprecated, refrain from checking Common version at runtime;
 * `Doctrine\Common\CommonException` is deprecated without replacement.

In addition to that, there will be no `doctrine/common` 3.0 and the package
will be gradually phased out.

Version 2.x will be maintained at least until ORM 3.0 is released, ensuring
compatibility with the latest PHP and providing bugfixes, but it will
no longer ship any new features.

For complete release notes,
[visit GitHub](https://github.com/doctrine/common/releases/tag/v2.9.0).

### DBAL 2.8.0

DBAL 2.8.0 is a minor release of Doctrine DBAL that aggregates over
30 fixes and improvements developed over the last 3 months.

The dependency on `doctrine/common` is removed. DBAL now
depends on `doctrine/cache` and `doctrine/event-manager` instead.

For complete release notes,
[visit GitHub](https://github.com/doctrine/dbal/releases/tag/v2.8.0).

### ORM 2.6.2

ORM 2.6.2 comes as a regular bugfix release.

It no longer uses the long ago deprecated Lexer and Inflector from `doctrine/common`.

For complete release notes,
[visit GitHub](https://github.com/doctrine/doctrine2/releases/tag/v2.6.2).
