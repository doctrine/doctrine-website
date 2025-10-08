---
title: "An update on the ORM 2 End of Life"
authorName: Benjamin Eberlei
authorEmail: kontakt@beberlei.de
permalink: /2025/10/08/an-update-on-the-orm-2-end-of-life.html
---

As part of our Core team meetup in Paris this October 2025, we discussed what the next steps are for ORM 2 and its end of life. 

ORM 3 was released almost 2 years ago, and we initially planned with the ORM 2 EOL for February 2026. 

So far adoption of ORM 3 is about 25-30% of ORM 2 based on Packagist installation numbers, which is not a big enough number. DBAL adoption of major 3 is much higher at roughly 60%.

Therefore we are going to push out the ORM 2 End of Life to at least February 2027, but we will restrict what kind of changes are eligible for the 2.x branch further.

Starting March 2026, we will only merge changes of the following kind into 2.x branches:

* PHP version compatibility, let's say support for PHP 8.5, 8.6 or 9.0
* Security fixes
* Improving forward compatibility with ORM 3

But we recommend that you take this next year to plan the upgrade for your applications.

We have made the upgrade smoother, by taking back some deprecations since the initial 3.0 release (namely partial objects), and improved the upgrade path for others. We also improved the [upgrade guides to DBAL 3](https://github.com/doctrine/dbal/blob/3.10.x/UPGRADE.md) and [upgrade guides to ORM 3](https://github.com/doctrine/orm/blob/3.5.x/UPGRADE.md) with further information.

We also ask you to help us understand and improve forward compatibility by participating in this discussion [What is blocking you from upgrading from ORM 2 to ORM 3? \#12193](https://github.com/doctrine/orm/discussions/12193)
