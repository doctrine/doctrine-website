---
title: "Doctrine Database Abstraction Layer"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: jwage
authorEmail:
categories: []
permalink: /2009/05/28/doctrine-database-abstraction-layer.html
---
One of the greatest advantages to using Doctrine is what is under the
hood that powers it. That is the database abstraction layer. This
powerful layer is based off of a lot of different code from various
popular PHP projects such as PEAR MDB2 and Zend\_Db. It provides you a
nice intuitive interface for retrieving information about your database
schema as well as issuing DDL statements to change it.

Public Method Index
===================

The database abstraction layer features the following methods in 1.x and
some new methods added in 2.0.

1.x Methods
-----------

-   listDatabases()
-   listFunctions()
-   listTriggers()
-   listSequences()
-   listTableConstraints()
-   listTableColumns()
-   listTableIndexes()
-   listTables()
-   listUsers()
-   listViews()
-   dropDatabase()
-   dropTable()
-   dropIndex()
-   dropConstraint()
-   dropForeignKey()
-   dropSequence()
-   createDatabase()
-   createTable()
-   createSequence()
-   createConstraint()
-   createIndex()
-   createForeignKey()
-   alterTable()

New Methods in 2.0
------------------

-   renameTable()
-   addTableColumn()
-   removeTableColumn()
-   changeTableColumn()
-   renameTableColumn()
-   dropView()
-   createView()

Some 2.0 Examples
=================

Here are some code examples using the Doctrine 2.0 Database Abstraction
Layer. We'll show you how you can execute these examples yourself by
checking out Doctrine 2.0 from SVN and setting up a PHP script to test
with.

> **TIP** The database abstraction layer can be used standalone so if
> you just want a nice standard layer for communicating with your
> database then Doctrine works for you too.

Checkout Doctrine
-----------------

    $ svn co http://svn.doctrine-project.org/trunk doctrine2
    $ cd doctrine2

Create Test Script
------------------

Create a new PHP script anywhere on your server and name it `test.php`.
Now lets initialize Doctrine so we can work with it. First we need to
set our `ClassLoader`.

~~~~ {.sourceCode .php}
<?php
// test.php

require 'lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader();
$classLoader->setBasePath('Doctrine', __DIR__ . '/lib');
~~~~

Create Connection
-----------------

Now we need to create our `Connection` instance.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'dbname' => 'mysql',
    'user' => 'root',
    'password' => ''
);
$driver = new \Doctrine\DBAL\Driver\PDOMySql\Driver;
$conn = new \Doctrine\DBAL\Connection($connectionOptions, $driver);

**NOTE** Notice how we are temporarily connecting to the special
database for MySQL named ``mysql``. We'll use this database to
connect to so we can create our test database.
~~~~

Now we can simply retrieve the `SchemaManager` instance from the
`Connection` driver and begin to execute some of the methods we listed
above.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$sm = $conn->getSchemaManager();
~~~~

The first thing we could do is create the database by calling the
`createDatabase()` method on the `$sm` instance.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$sm->createDatabase('doctrine2test');
~~~~

Now that we have the database created, change your `$connectionOptions`
key `dbname` to specify `doctrine2test` so that we connect to the new
database that we just created.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'dbname' => 'doctrine2test',
    'user' => 'root',
    'password' => ''
);

// ...
~~~~

Now we can begin adding things to the new database. For example we could
issue a command to create a new table.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$columns = array(
    'id' => array(
        'type' => \Doctrine\DBAL\Type::getType('integer'),
        'autoincrement' => true,
        'primary' => true,
        'notnull' => true
    ),
    'test' => array(
        'type' =>  \Doctrine\DBAL\Type::getType('string'),
        'length' => 255
    )
);

$options = array();

$sm->createTable('new_table', $columns, $options);
~~~~

Then after creating the table I can later add a new column to it.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$column = array(
    'type' =>  \Doctrine\DBAL\Type::getType('string'),
    'length' => 255
);

$sm->addTableColumn('new_table', 'new_column', $column);
~~~~

Or I could even drop the table completely.

~~~~ {.sourceCode .php}
<?php
// test.php

// ...

$sm->dropTable('new_table');

**NOTE** Not all of the above listed methods are supported by every
single DBMS. If your DBMS does not support the functionality then
Doctrine will throw exceptions accordingly.
~~~~

All the above example are very simple schema changes but Doctrine is
capable of manipulating very complex schemas from a standardized
programmatic interface. The Doctrine Migrations extension makes use of
this layer heavily to do all the operations for changing your database
schemas.
