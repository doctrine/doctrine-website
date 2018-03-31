---
title: Using Views with Doctrine
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
indexed: false
---
I've seen a few requests recently on how you can use a view with
Doctrine. This is very easy and I've also learned a few neat tricks
that you can do to accomplish abnormal things while writing this
article.

Creating the View
-----------------

First I will demonstrate how you can turn a normal
``Doctrine_Query`` instance in to a view. This is just as easy as
creating an instance of ``Doctrine_View`` and setting a reference
between the query and the view.

.. code-block:: php

    <?php
    $q = Doctrine::getTable('BlogPost')
      ->createQuery('p')
      ->select('p.*, COUNT(c.id) as num_comments')
      ->leftJoin('p.Comments c')
      ->orderBy('p.id DESC')
      ->groupBy('p.id');
    
    $view = new Doctrine_View($q, 'test_view');

To create the view in the database you can call the
``Doctrine_View::create()`` method.

.. code-block:: php

    <?php
    $view->create();

    **TIP** You can drop the view just the same by calling the
    ``Doctrine_View::drop()`` method.

.. code-block:: php

    <?php
        $view->drop();


Executing the View
------------------

Now when the ``Doctrine_Query`` instance above is executed, it will
execute the SQL for the view instead of parsing the DQL, generating
the SQL and executing it.

.. code-block:: php

    <?php
    $blogPosts = $q->execute();

Executing the above would execute the following SQL query.

::

    [sql]
    SELECT * FROM test_view

Tweaking the View
-----------------

Now here is where things get interesting. Say we wanted to take the
SQL that the above ``Doctrine_Query`` generates, and modify it
slightly with some custom SQL that otherwise could not make it
through the DQL parser.

We can get the SQL from the query, modify it, then manually create
the view in our database.

.. code-block:: php

    <?php
    echo $q->getSql();

The above would output the following SQL.

::

    [sql]
    SELECT b.id AS b__id, b.title AS b__title, b.excerpt AS b__excerpt, b.body AS b__body, COUNT(c.id) AS c__0 FROM blog_post b LEFT JOIN comment c ON b.id = c.blog_post_id GROUP BY b.id ORDER BY b.id DESC

Now lets say we wanted to add something to the SQL that is
proprietary to your DBMS, or is some complex SQL that won't make it
through the DQL parser. We can modify the above SQL then re-create
the view with that SQL manually. Let's make a simple change and add
the ``USE INDEX`` keyword to force MySQL to use a certain index for
the query.

    **NOTE** The example I have chosen is a very simple one only to
    demonstrate the capabilities. This example may not be a real world
    scenario for you. The only purpose of me showing this is to open a
    door for you to solve potential problems for you in the future.


::

    [sql]
    SELECT b.id AS b__id, b.title AS b__title, b.excerpt AS b__excerpt, b.body AS b__body, COUNT(c.id) AS c__0 FROM blog_post b LEFT JOIN comment c USE INDEX (blog_post_id_idx) ON b.id = c.blog_post_id GROUP BY b.id ORDER BY b.id DESC;

Now lets take this query and manually create the view with it.

    **NOTE** We must first drop the view as we already created it once
    in a previous step. This is just as easy as issuing the DROP VIEW
    command to MySQL. Afterward, re-create the view again with the
    modified SQL.


::

    [sql]
    DROP VIEW test_view;
    CREATE VIEW test_view AS SELECT b.id AS b__id, b.title AS b__title, b.excerpt AS b__excerpt, b.body AS b__body, COUNT(c.id) AS c__0 FROM blog_post b LEFT JOIN comment c USE INDEX (blog_post_id_idx) ON b.id = c.blog_post_id GROUP BY b.id ORDER BY b.id DESC;

Now when we execute the code in the first part of this article it
will execute the view which contains the customized SQL.

.. code-block:: php

    <?php
    $blogPosts = $q->execute();

    **CAUTION** If you customize the SQL, it must maintain the same
    structure, aliases, etc. in order for Doctrine to be able to
    hydrate the data in to the object graph.


That is it! Now you can easily use some custom SQL in your queries
as views. The benefit of using a view is that it is easily reusable
and it is much faster than executing a normal query in most cases.
