---
title: "Database-Driver Support in Doctrine 2"
menuSlug: blog
layout: blog-post
authorName: beberlei
authorEmail:
categories: []
permalink: /2010/02/11/database-support-doctrine2.html
---
Not only the ORM part of Doctrine will see major changes for the step
from the 1.x to the Doctrine 2 series. The DBAL layer has undergone
major refactorings and there has been a very throughout separation of
concerns. Any database platform that will be supported has to extend
four different classes/interfaces.

-   Doctrine and Doctrine - Both interfaces implement the interference
    between PHP and the Database, they are the lowest layer of any
    platform support.
-   Doctrine - This abstract class requires you to specify the
    specialites of the SQL dialect of the database-platform you are
    going to implement.
-   Doctrine - This abstract class defines the interaction of the
    database platform to create a database schema, for example in
    combination with the ORM SchemaTool.

For the Doctrine 2.0 release we plan to support 4 different platforms,
all tested in-depth:

-   MySQL using the PDO Mysql extension
-   PgSQL using the PDO PostgresSQL extension
-   Oracle using the OCI extension
-   Sqlite using the PDO SQLite extension

Both the SchemaManager and Platform can be re-used for any Driver that
is connected to the database. If you would want to use Mysqli instead of
PDO MySQL you would only need to implement a new Driver and Statement.
And if you just want to change some of the sql specific details in
regard to schema generation you would only need to extend the
AbstractPlatform.

Still, from a database-platform point of view the default support is
lacking, for example MsSql support with both PDO and SqlSrv is currently
missing. Firebird or IBM Db2 are other platforms that are wide-spread
and not supported currently. However we don't want to rush only
half-finished support into Doctrine 2. That is where you come in: We
would greatly appreciate any help in getting support for any new
database platform into Doctrine 2.

For the implementation of a completly new database platform you can rely
on the powerful PHPUnit Testsuite of Doctrine 2. There are lots of tests
that check the functionality of your driver, platform and schema
implementations against various scenarios. Additionally the complete ORM
functional test-suite can run against your new database platform.
Furthermore you can count of everyone in the Doctrine DEV Team for help,
we are hanging around on Freenode IRC in the \#doctrine-dev Channel. You
could also create a ticket on Jira and attach a patch or just discuss
your ideas.
