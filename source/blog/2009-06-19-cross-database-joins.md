---
title: "Cross Database Joins"
authorName: jwage
authorEmail:
categories: []
permalink: /2009/06/19/cross-database-joins.html
---
Cross Database Joins
====================

In Doctrine, joining data across databases is not technically
"supported" by a designed feature, but you can make it work by tricking
Doctrine a little bit.

In this article I'll show you how you can setup a database schema that
specifies relationships across two databases and then issue a query
which joins data from these two databases.

I used the Doctrine sandbox to prepare this test so if you want to try
it, you can use it too.

Database Connections
====================

First lets setup our two database connections we'll use to query from.
Modify the `config.php` file included with the sandbox and replace the
default single connection with the following code.

~~~~ {.sourceCode .php}
<?php
Doctrine_Manager::connection('mysql://root@localhost/doctrine_test1', 'doctrine_test1');
Doctrine_Manager::connection('mysql://root@localhost/doctrine_test2', 'doctrine_test2');
~~~~

Schema
======

Now lets define our YAML schema file that we'll use to run our tests
against. You can modify the `config/doctrine/schema.yml` file and
include the following YAML.

    [yml]
    User:
      tableName: doctrine_test1.user
      connection: doctrine_test1
      columns:
        username: string(255)
        password: string(255)

    Profile:
      tableName: doctrine_test2.profile
      connection: doctrine_test2
      columns:
        user_id: integer
        first_name: string(255)
        last_name: string(255)
      relations:
        User:
          foreignType: one
          onDelete: CASCADE

    **NOTE** Notice how we specify the full table name, including the
    name of the database. Currently, Doctrine does not generate the SQL
    that includes the database name. It only includes the table name,
    but we can trick Doctrine by simply specifying the
    ``database_name.table_name`` as the table name.

Test Data Fixtures
==================

In the `data/fixtures` directory create a `data.yml` file and paste the
following fixtures inside so we can have some data in each database to
run our tests against.

    [yml]
    User:
      jwage:
        username: jwage
        password: changeme
        Profile:
          first_name: string(255)
          last_name: string(255)

Build the Database
==================

Now lets build our database and import the data fixtures from above.
This can be easily done by running the following from the Doctrine
command line interface.

    $ php doctrine build-all-reload

Run the Test
============

Now we have our models created, we have our database created and we have
our test fixtures loaded in to the database. Now it is time to run some
sample code and see what we get!

First lets write our `Doctrine_Query` and look at the generated SQL.
Paste the following code in to index.php and lets execute it!

~~~~ {.sourceCode .php}
<?php
$q = Doctrine::getTable('User')
  ->createQuery('u')
  ->leftJoin('u.Profile p');

echo $q->getSql();
~~~~

The above code would output the following SQL query.

    [sql]
    SELECT d.id AS d__id, d.username AS d__username, d.password AS d__password, d2.id AS d2__id, d2.user_id AS d2__user_id, d2.first_name AS d2__first_name, d2.last_name AS d2__last_name FROM doctrine_test1.user d LEFT JOIN doctrine_test2.profile d2 ON d.id = d2.user_id

    **NOTE** Notice how in the above SQL that is generated it include
    the database name and the table name. So now the query is able to
    join across databases if your RDBMS supports it.

Now lets execute the above query and look at the results.

~~~~ {.sourceCode .php}
<?php
$q = Doctrine::getTable('User')
  ->createQuery('u')
  ->leftJoin('u.Profile p');

$users = $q->fetchArray();

print_r($users);
~~~~

The above would output just exactly what you'd expect.

    Array
    (
        [0] => Array
            (
                [id] => 1
                [username] => jwage
                [password] => changeme
                [Profile] => Array
                    (
                        [id] => 1
                        [user_id] => 1
                        [first_name] => string(255)
                        [last_name] => string(255)
                    )

            )

    )

The data from the `User` model came from one database, and the data from
the `Profile` model came from the other database.

> **NOTE** This will only work if your database supports foreign keys
> and joins across databases. I know MySQL does support this but I am
> unsure about others. This same method can be used to query for data
> across PostgreSQL schemas too.

That is it! Joining data from across different databases is no problem
in Doctrine.

> **CAUTION** This is not a designed feature of Doctrine and you may
> experience edge cases that may not work as you'd expect. This is just
> useful if you need to join data across databases and if you experience
> edge cases you can work around them in your project.
