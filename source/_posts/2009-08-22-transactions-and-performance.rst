---
title: Transactions and Performance
menuSlug: blog
authorName: romanb 
authorEmail: 
categories: []
indexed: false
---
In this post I want to clarify some things about transactions and
performance of PHP applications in general. I want to show you that
it is very easy to lose a lot of performance without using any
"heavy" framework at all and I also want to show that frameworks
can actually help you avoid a lot of these problems transparently.
I want to sensibilize you for the fact that there are a lot of
factors in PHP application performance and that you can very easily
lose the performance that you hoped to gain by not using framework
X or Z or building your own ("Not Invented Here"-syndrome) by
several orders of magnitude through rather trivial errors or
misconceptions.

Given a MySql database with InnoDB tables, which of the following
code snippets that insert 20 users do you think is faster? First
the Doctrine (2) version:

.. code-block:: php

    <?php
    // Using Doctrine 2 to insert 20 users
    for ($i=0; $i<20; ++$i) {
        $user = new CmsUser;
        $user->name = 'Guilherme';
        $user->status = 'Slave';
        $user->username = 'gblanco';
        $em->persist($user);
    }
    
    $s = microtime(true);
    $em->flush();
    $e = microtime(true);
    echo ($e - $s) . "<br/>";

Now the good old mysql\_query version:

.. code-block:: php

    <?php
    $s = microtime(true);
    for ($i=0; $i<20; ++$i) {
        mysql_query("INSERT INTO cms_users (name, status, username) VALUES ('Guilherme', 'Slave', 'gblanco')", $link);
    }
    $e = microtime(true);
    echo ($e - $s) . "<br/>";

Even this comparison is not fair since flush() is doing a lot more
stuff but anyhow. The results might surprise some of you:

::

    Doctrine 2: 0.0094 seconds
    mysql_query: 0.0165 seconds

Yes, our good old mysql\_query code is almost twice as slow even
though it does a lot less, provides no features, no abstraction, no
basic protection against SQL injection, etc. Why is that? The
answer is: transactions. In the Doctrine 2 example, Doctrine takes
over transaction management for us and efficiently executes all
inserts in a single, short transaction. In the plain mysql\_query
example, there is no transaction demarcation and since MySql by
default operates in autocommit mode, every mysql\_query call will
implicitly commit the transaction and start a new one. Thats 20
transactions. Here is the revised code of the second example with
proper transaction demarcation:

.. code-block:: php

    <?php
    $s = microtime(true);
    mysql_query('START TRANSACTION', $link);
    for ($i=0; $i<20; ++$i) {
        mysql_query("INSERT INTO cms_users (name, status, username) VALUES ('Guilherme', 'Slave', 'gblanco')", $link);
    }
    mysql_query('COMMIT', $link);
    $e = microtime(true);
    echo ($e - $s) . "<br/>";

The result:

::

    mysql_query: 0.0028 seconds

Thats a huge difference. We can conclude:

**Bad or no transaction management/demarcation can reduce performance by several orders of magnitude.**

Many people are used to autocommit mode without really being aware
of what it is doing. It does not mean there is no transaction
unless you issue START/BEGIN TRANSACTION or PDO#beginTransaction().
It means after every single query a transaction is committed
automatically and a new one started. Methods like
PDO#beginTransaction() merely suspend autocommit mode for a short
duration (until you call PDO#commit()/PDO#rollback()).

To clarify:

**You can not talk to your database outside of a transaction.**

Even SELECT queries get wrapped in a small transaction in
autocommit mode. However since SELECT statements usually don't
result in any write locks (like INSERT/UPDATE/DELETE) the penalty
of these transactions is usually not that big.

Doctrine 2 can help a lot here. You can modify your objects
anywhere, persist and delete objects anywhere and once you call
``EntityManager#flush()`` Doctrine 2 will efficiently make all
updates in a single transaction.

What I wanted to highlight with this post is that there are a lot
of factors that influence the performance of your application, and
raw execution speed of the code is certainly not one of the most
influential ones. You can very easily lose 10 times the performance
by trivial things such as the one shown above (bad/no transaction
demarcation) than what you gained by choosing some "ultra
lightweight" PHP framework or a homegrown solution.

There are many more factors, like network load, inefficient
database indices or no indices, and much more. Don't just always
look at the raw execution speed of your code. Use code that is well
tested, established, used by lots of people and developed by lots
of people. Don't reinvent the wheel and use existing tools or help
make existing tools better! (Oh, and use the right tool for the
job, of course!)

Most of the time when you think your own solutions are much better
and have a lot less bugs than existing ones then thats most likely
just because noone else is using it and so the bugs are never found
:-).

PS: If you're still confused by the autocommit mode, let me
recommend this excellent page from the Hibernate project:
`Non-transactional data access and the auto-commit mode <https://www.hibernate.org/403.html>`_
