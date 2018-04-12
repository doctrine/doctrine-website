---
title: Maintenance Releases 2.0.5 for DBAL and ORM
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: [release]
permalink: /2011/05/14/doctrine-maintenance-may11.html
---
Slightly behind schedule we released the next round of maintenance
versions of Doctrine DBAL (2.0.5) and ORM (2.0.5) today. It also
includes a Security fix for DBAL in combination with PDO MySQL and
charsets that was closed in PHP 5.3.6. If you are using 5.3.6, you
should now use the "charset" option in DriverManager::getConnection()
instead of the MysqlSessionInit listener.

-   [DBAL Changelog (4 Tickets
    closed)](http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10132)
-   [ORM Changelog (15 Tickets
    closed)](http://www.doctrine-project.org/jira/browse/DDC/fixforversion/10133)

You can grab the packages from the download page or our [Github
repository](http://github.com/doctrine).

Please report any problems to the Jira Bugtracker.
