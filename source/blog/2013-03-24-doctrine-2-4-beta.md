---
title: "Doctrine 2.4 Beta1 released"
authorName: Benjamin Eberlei
authorEmail:
categories: [release]
permalink: /2013/03/24/doctrine-2-4-beta.html
---
**24.03.2013**

We have released the first beta version of Doctrine 2.4. Some of the
changes are listed in [this
talk](https://speakerdeck.com/asm89/what-is-new-in-doctrine) by
Alexander and me from Symfony Live Berlin last year.

You can find a list of changes on
[Jira](http://www.doctrine-project.org/jira/issues/?jql=project%20in%20(DDC%2C%20DBAL%2C%20DCOM)%20AND%20fixVersion%20%3D%20%222.4%22%20AND%20status%20%3D%20Resolved%20ORDER%20BY%20priority%20DESC).

You can install the release with [Composer](http://www.packagist.org):

    {
        "require": {
            "doctrine/orm": "2.4.0-beta1",
            "doctrine/dbal": "2.4.0-beta1",
            "doctrine/common": "2.4.0-rc1"
        }
    }

Please test this release with your existing applications to allow us to
find BC breaks and remove them before the final release. The plan is to
release the final version in the next 4-6 weeks.

In these next weeks we will work to incorporate all changes in the
documentation.
