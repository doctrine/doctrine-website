---
title: "Doctrine 2.2 Beta 2"
authorName: Benjamin Eberlei
authorEmail:
categories: []
permalink: /2012/01/03/doctrine2-2-beta2.html
---
Sadly we did not manage to hold our schedule with a 2.2 release in 2011,
we had to do some larger changes before the final release. This means we
are releasing another Beta of Doctrine DBAL and ORM. The final release
is rescheduled to 19th January.

-   DBAL Changelog
    \<[https://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10189](https://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10189)\>\`\_
-   ORM Changelog
    \<[https://www.doctrine-project.org/jira/browse/DDC/fixforversion/10188](https://www.doctrine-project.org/jira/browse/DDC/fixforversion/10188)\>\`\_

Please try and test this code with your production applications and
report any backwards compatibility breaks to the [Bug
Tracker](https://www.doctrine-project.org/jira) or the Mailing List. See
the
[UPGRADE\_2\_2](https://github.com/doctrine/doctrine2/blob/master/UPGRADE_TO_2_2)
file to see backwards incompatible changes.

You can install the Beta through
[Github](https://github.com/doctrine/doctrine2) ,
[PEAR](http://pear.doctrine-project.org) by download or through
[Composer](https://packagist.org):

> {
> :   "require": { "doctrine/orm": "2.2.0-BETA2" }
>
> }
