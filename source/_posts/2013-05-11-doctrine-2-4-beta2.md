---
title: Doctrine 2.4 Beta2 released
menuSlug: blog
authorName: Benjamin Eberlei 
authorEmail: 
categories: [release]
permalink: /2013/05/11/doctrine-2-4-beta2.html
---
**11.05.2013**

We have released the second beta version of Doctrine 2.4. Some of the
changes are listed in [this
talk](https://speakerdeck.com/asm89/what-is-new-in-doctrine) by
Alexander and me from Symfony Live Berlin last year.

Most of the new functionality is already documented and marked with
"Since 2.4". We will also prepare a blog post for the release that shows
all the new functionality in one place.

You can find a list of changes on
[Jira](http://www.doctrine-project.org/jira/issues/?jql=project%20in%20(DDC%2C%20DBAL%2C%20DCOM)%20AND%20fixVersion%20%3D%20%222.4%22%20AND%20status%20%3D%20Resolved%20ORDER%20BY%20priority%20DESC).

You can install the release with [Composer](http://www.packagist.org):

    {
        "require": {
            "doctrine/orm": "2.4.0-beta2",
            "doctrine/dbal": "2.4.0-beta2",
            "doctrine/common": "2.4.0-rc2"
        }
    }

Please test this release with your existing applications to allow us to
find BC breaks and remove them before the final release. This will be
the last beta before Release Candidates will be created. We expect to
need only one RC and that the final release will follow shortly.

You should make yourself familiar with the UPGRADE documents, as we had
to make some backwards compatibility breaks to fix nasty bugs:

-   [ORM](https://github.com/doctrine/doctrine2/blob/master/UPGRADE.md)

