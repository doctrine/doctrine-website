---
title: "Doctrine 2 Batch Processing"
authorName: romanb
authorEmail:
categories: []
permalink: /2009/08/07/doctrine2-batch-processing.html
---
**TIP** Disclaimer: In general, an ORM is surely not the best tool
:   for the job for mass data movements, however, it can be a convenient
    alternative if the performance is sufficient. Every RDBMS has its
    own highly efficient commands for such operations. For maximum
    efficiency you should consult the manual of your RDBMS.

    **NOTE** The hardware used for the following tests: MBP, Intel Core
    2 Duo 2.53 Ghz, 4GB DDR3 RAM, 7200rpm HDD.

A lot of people have encountered the infamous "memory exhausted" errors
when using Doctrine for bulk operations or in long-running scripts. This
is a problem that is the result of 2 things:

-   The often unnecessarily intertwined architecture of Doctrine 1.x.
-   The simple garbage collector of PHP \< 5.3.

    > **NOTE** If you did not know it yet, basically every circular
    > reference between objects (and that is every bidirectional
    > relationship!) is a potential memory leak in PHP \< 5.3.

Now, the PHP team did their homework and introduced a new garbage
collector in PHP 5.3 (that is enabled by default, thank god!) that is
capable of properly detecting cyclic references that are not referenced
any longer from the outside.

In Doctrine 2 we did our part of the homework to address this issue and
redesigned the Doctrine core from scratch. The result is a much better
user experience, as I will demonstrate with some examples.

Mass inserts
============

The first thing we will look at is mass inserts. In Doctrine 2 there is
a special pattern that can be used for mass inserts that looks as
follows:

~~~~ {.sourceCode .php}
<?php
// $em instanceof EntityManager
$batchSize = 20;
for ($i=1; $i<=10000; ++$i) {
     $obj = new MyEntity;
     $obj->setFoo('...');
     // ... set more data
     $em->persist($obj);
     if (($i % $batchSize) == 0) {
         $em->flush();
         $em->clear();
    }
}
~~~~

I used this pattern to create a little demonstration that looks like
this in a PHPUnit test:

~~~~ {.sourceCode .php}
<?php
// $this->_em created in setUp()
echo "Memory usage before: " . (memory_get_usage() / 1024) . " KB" . PHP_EOL;
$s = microtime(true);
$batchSize = 20;
for ($i=1; $i<=10000; ++$i) {
     $user = new CmsUser;
     $user->status = 'user';
     $user->username = 'user' . $i;
     $user->name = 'Mr.Smith-' . $i;
     $this->_em->persist($user);
     if (($i % $batchSize) == 0) {
         $this->_em->flush();
         $this->_em->clear();
    }
}

//gc_collect_cycles(); // explained later!
echo "Memory usage after: " . (memory_get_usage() / 1024) . " KB" . PHP_EOL;

$e = microtime(true);
echo ' Inserted 10000 objects in ' . ($e - $s) . ' seconds' . PHP_EOL;
~~~~

As you can see, we insert a total of 10000 objects. On running this code
through PHPUnit, using an SQLite in-memory database, I got the following
output:

    Memory usage before: 5034.03515625 KB
    Memory usage after: 6726.3515625 KB
    Inserted 10000 objects in 3.165983915329 seconds

We can see the following:

-   The insertion of 10000 objects through Doctrine into an SQLite
    in-memory database took roughly 3 seconds, not too bad.
-   Memory usage increased by roughly 1.7MB, not too bad either.

If you are now still not satisfied and wonder where the 1.7MB are going,
here is the answer: A small part of that is occupied by objects that
were created on-demand internally by Doctrine, these will stay pretty
constant, however. But the majority of this 1.7MB has simply not yet
been reclaimed (but is eliglible for garbage collection for the new
garbage collector!). To prove that, I can just uncomment the
gc\_collect\_cycles() function call. Here's the result:

    Memory usage before: 5034.3828125 KB
    Memory usage after: 5502.21484375 KB
    Inserted 10000 objects in 3.1807188987732 seconds

Much better! And to prove that the \~500KB occupied by Doctrine are
constant, I simply made it 20000 objects. Here is the result:

    Memory usage before: 5034.3828125 KB
    Memory usage after: 5502.21484375 KB
    Inserted 20000 objects in 6.6149919033051 seconds

We can see the following things:

-   Memory usage is constant, the second batch of 10000 objects did not
    result in additional memory usage.
-   The mass insertion strategy scales almost linearly. 10k objects took
    \~3.2 seconds and 20k objects took \~6.6 seconds.

Note: You do not really need to call gc\_collect\_cycles(). This should
just demonstrate that the memory can be reclaimed. PHP would reclaim
that memory anyway when it needs to.

Even better, when testing the peak memory usage
(memory\_get\_peak\_usage()) it turned out that the memory usage never
grew beyond \~10MB in between. If you choose a larger batch size the
peak memory usage will be higher and vice versa.

Mass object processing
======================

Now we take a look at mass-processing objects, which means loading 10000
objects from the database and doing something with each of them. The
clue here is the new support for iterative (step-by-step) hydration in
Doctrine 2. The pattern for these kinds of tasks looks as follows:

~~~~ {.sourceCode .php}
<?php
$q = $this->_em->createQuery("<DQL to select the objects I want>");
$iterableResult = $q->iterate();
while (($row = $iterableResult->next()) !== false) {
        // do stuff with the data in the row, $row[0] is always the object
        $this->_em->detach($row[0]); // detach from Doctrine, so that it can be GC'd immediately
 }
~~~~

So instead of using `$q->execute()` or `$q->getResult()` or similar, we
use `$q->iterate()` which returns an instance of `IterableResult` that
allows us to iterate over the result step by step. The important part
for not running out of memory is the line where the created object is
detached from Doctrine, which results in Doctrine removing any internal
references to that object, Doctrine no longer "knows" about that object.

I used this pattern to iterate through the just inserted 10000 objects
as follows:

~~~~ {.sourceCode .php}
<?php
$q = $this->_em->createQuery("select u from Doctrine\Tests\Models\CMS\CmsUser u");
$iterableResult = $q->iterate();

echo "Memory usage before: " . (memory_get_usage() / 1024) . " KB" . PHP_EOL;

while (($row = $iterableResult->next()) !== false) {
    // ... I could do some stuff here
    $this->_em->detach($row[0]);
}

echo "Memory usage after: " . (memory_get_usage() / 1024) . " KB" . PHP_EOL;
~~~~

The following is the result:

    Memory usage before: 6578.58984375 KB
    Memory usage after: 6581.71875 KB

The result is pretty acceptable. Here is the same again, this time for
20000 objects, again to prove that the small memory increase is
constant:

    Memory usage before: 6578.23828125 KB
    Memory usage after: 6581.359375 KB

Good stuff!

> **NOTE** If you're thinking that I waited ages until the 10k or 20k
> objects were hydrated, that was not the case. 10k or 20k objects
> (without associations) are hydrated in seconds.

More information on bulk operations with Doctrine 2 can be found in the
(very new) online manual that is still a work in progress:

http://www.doctrine-project.org/documentation/manual/2\_0/en/batch-processing

UPDATE
======

Some people seem to be wondering why Doctrine does not use multi-inserts
(insert into (...) values (...), (...), (...), ...

First of all, this syntax is only supported on mysql and newer
postgresql versions. Secondly, there is no easy way to get hold of all
the generated identifiers in such a multi-insert when using
AUTO\_INCREMENT or SERIAL and an ORM needs the identifiers for identity
management of the objects. Lastly, insert performance is rarely the
bottleneck of an ORM. Normal inserts are more than fast enough for most
situations and if you really want to do fast bulk inserts, then a
multi-insert is not the best way anyway, i.e. Postgres COPY or Mysql
LOAD DATA INFILE are several orders of magnitude faster.

These are the reasons why it is not worth the effort to implement an
abstraction that performs multi-inserts on mysql and postgresql in an
ORM.

I hope that clears up some questionmarks.
