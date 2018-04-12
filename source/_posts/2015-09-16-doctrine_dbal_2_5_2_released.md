---
title: Doctrine DBAL 2.5.2 released
menuSlug: blog
authorName: Steve Müller
authorEmail: deeky666@googlemail.com
categories: []
permalink: /2015/09/16/doctrine_dbal_2_5_2_released.html
---
We are happy to announce the immediate availability of Doctrine DBAL
2.5.2.

This version fixes a regression where dropping a database on PostgreSQL
didn't work properly anymore as well as several other issues.

You can find all the changes on JIRA:

-   [DBAL
    2.5.2](http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10731)
    - 24 issues fixed

You can install the DBAL using Composer and the following
`composer.json` contents:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/dbal": "2.5.2"
    }
}
~~~~

Please report any issues you may have with the update on the mailing
list or on [Jira](http://www.doctrine-project.org/jira).
