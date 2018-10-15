---
title: "Release of DBAL 2.0.0 Beta3"
authorName: beberlei
authorEmail:
categories: []
permalink: /2010/07/27/dbal2-beta3-released.html
---
In preparation of the next ORM Beta Release in the next days we already
released Doctrine DBAL 2.0.0BETA3 today. Noteworthy changes include:

-   BC Break: Changed behaviour of Postgres and Oracle DateTime now
    without Timezone (TIMESTAMP WITHOUT TIME ZONE instead of TIMESTAMP
    WITH TIME ZONE) See [Ticket
    DBAL-22](http://www.doctrine-project.org/jira/browse/DBAL-22) for
    more details aswell as migration issues for PostgreSQL and Oracle.
    This will be re-announced for the ORM Beta 3 release, which is
    affected from this change.
-   SQL Loggers can now log execution times of queries
    [DBAL-11](http://www.doctrine-project.org/jira/browse/DBAL-11)
-   Several Issue with AutoIncrement Detection in Doctrine

A total of 13 issues on DBAL has been fixed.

Links:

-   [Changelog](http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10066)
-   [Installation](http://www.doctrine-project.org/projects/dbal/2.0/download/2.0.0BETA3)

    > **NOTE** DBAL BETA 3 is not currently compatible with the ORM
    > master, we still have to make some changes. Please wait to use
    > DBAL Beta 3 with ORM until the ORM Beta 3 is released.


