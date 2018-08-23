---
title: "PHP Benchmarking Mythbusters"
menuSlug: blog
layout: blog-post
authorName: romanb
authorEmail:
categories: []
permalink: /2009/11/18/php-benchmarking-mythbusters.html
---
First of, this blog post sucks. I thought I would never write such a
senseless apples and oranges comparison with artificial and meaningless
benchmarks, but I was just a bit astonished by the results that I would
like to share.

I use object-relational mapping tools in many different languages, from
Java to C\# to PHP. One of the many supposedly lightweight alternatives
in PHP to Doctrine is Outlet. After stumbling upon this comment on a
[stackoverflow.com
post](http://stackoverflow.com/questions/185358/simple-php-orm):
**"Gotta second Outlet. Doctrine is comically bloated - it is WAY too
big to be a sensible choice for anything but the lightest of server
loads."** I thought I take a look at Outlet. This ORM seems to consist
of only 9 classes! It can't get any more lightweight right? I assumed it
would blow Doctrine out of the water performance-wise in all situations.

So I downloaded Outlet 0.7 and created a simple test database with just
1 table. Then I wrote a small script that bootstraps Outlet, inserts 500
objects into the database and reads them out afterwards (Yes, it's
stupid, just like most other artificial benchmarks).

Environment is PHP 5.3.0 with APC.

~~~~ {.sourceCode .php}
<?php
    echo PHP_EOL . (memory_get_usage() / 1024) . ' KB ' . PHP_EOL;

include 'config.php';

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/outlet');

require 'Outlet.php';

ini_set('error_reporting', E_ALL);

class Bug
{
    public $ID;
    public $Title;
    public $Description;
}

$config = array(
  'connection' => array(
    'dsn'      => 'mysql:host=localhost;dbname=outletbenchmark',
    'username' => $mysqlUsername,
    'password' => $mysqlPassword,
    'dialect' => 'mysql'
  ),
  'classes' => array(
    'Bug' => array(
      'table' => 'bugs',
      'props' => array(
        'ID'        => array('ID', 'int', array('pk' => true, 'autoIncrement' => true)),
        'Title'     => array('Title', 'varchar'),
        'Description' => array('Description', 'varchar')
      )
    )
  )
);
Outlet::init($config);
$outlet = Outlet::getInstance();
$outlet->createProxies();

$s = microtime(true);
$outlet->getConnection()->beginTransaction();
for ($i = 0; $i < 500; ++$i) {
    $bug = new Bug;
    $bug->Title = 'This is a test bug';
    $bug->Description = 'Hey there!';

    $outlet->save($bug);
}
$outlet->getConnection()->commit();

$e = microtime(true);
echo "\nInsert:" . ($e - $s) . "\n";

$outlet->clearCache();

$s = microtime(true);
$bugs = $outlet->select('Bug');
$e = microtime(true);

$outlet->clearCache();

echo "\nQuery:" . ($e - $s) . "\n";

echo "\n" . (memory_get_usage() / 1024) . ' KB ' . PHP_EOL;

**CAUTION** First off, ini\_set('error\_reporting', E\_ALL); was
necessary to silence the following E\_STRICT warnings coming from
Outlet:

::

    Strict Standards: Non-static method OutletMapper::get() should not be called statically,
    assuming $this from incompatible context in /Users/robo/dev/php/outlet/outlet-0.7/classes
    /outlet/Outlet.php on line 184

    Strict Standards: Only variables should be passed by reference in /Users/robo/dev
    /php/outlet/outlet-0.7/classes/outlet/OutletMapper.php on line 546

Does not really look good (and does not speak for Outlet very
well), but anyway.
~~~~

I did the same for Doctrine 2.0, *without setting up a metadata or query
cache*.

~~~~ {.sourceCode .php}
<?php
echo PHP_EOL . (memory_get_usage() / 1024) . ' KB ' . PHP_EOL;

include 'config.php';

require 'doctrine/Doctrine/Common/IsolatedClassLoader.php';

/**
 * @Entity
 * @Table(name="bugs")
 */
class Bug
{
    /** @Id @Column(type="integer") @GeneratedValue(strategy="AUTO") */
    public $ID;
    /** @Column(type="string") */
    public $Title;
    /** @Column(type="string") */
    public $Description;
}

$classLoader = new \Doctrine\Common\IsolatedClassLoader('Doctrine');
$classLoader->setBasePath(__DIR__ . '/doctrine');
$classLoader->register();

$config = new \Doctrine\ORM\Configuration;

$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');
$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'user' => $mysqlUsername,
    'password' => $mysqlPassword,
    'host' => 'localhost',
    'dbname' => 'doctrine2benchmark'
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$s = microtime(true);
for ($i = 0; $i < 500; ++$i) {
    $bug = new Bug;
    $bug->Title = 'BBug';
    $bug->Description = 'Hello there!';

    $em->persist($bug);
}
$em->flush();

$e = microtime(true);
echo "\nInsert:" . ($e - $s) . "\n";

$em->clear();

$s = microtime(true);
$bugs = $em->getRepository('Bug')->findAll();
$e = microtime(true);

$em->clear();

echo "\nQuery:" . ($e - $s) . "\n";

echo "\n" . (memory_get_usage() / 1024) . ' KB ' . PHP_EOL;
~~~~

Here are my results.

1st Run
=======

| Measurement | | Outlet | | Doctrine | | ------------ | |
----------------- | | ------------------- | | Insert Time | |
0.23142600059509 | | 0.11601996421814 | | Query Time | |
0.070523977279663 | | 0.025638818740845 | | Used Memory | | 644.5546875
KB | | 1061.83203125 KB |

No, I did not swap the numbers, I promise. You see that the D2 version
uses about 400KB more memory but the result of the timings are quite
surprising. Being curious I ran both scripts several times which means
the query section has to hydrate 500 objects more for each run.

2nd Run
=======

**1st refresh (1000 objects)**

| Measurement | | Outlet | | Doctrine | | ------------ | |
----------------- | | ------------------- | | Insert Time | |
0.26595592498779 | | 0.11661005020142 | | Query Time | |
0.14437794685364 | | 0.052286863327026 | | Used Memory | | 875.0703125
KB | | 1313.15625 KB |

3rd Run
=======

**2nd refresh (1500 objects)**

| Measurement | | Outlet | | Doctrine | | ------------ | |
----------------- | | ------------------- | | Insert Time | |
0.2314441204071 | | 0.11621117591858 | | Query Time | | 0.21359491348267
| | 0.079329013824463 | | Used Memory | | 1139.5859375 KB | | 1541.59375
KB |

Did you expect these results? After all Doctrine is so bloated, right?
(Doctrine 2 full package \~250 classes) and Outlet is so lightweight
(\~9 classes)?

Bottom line:

-   The number of classes barely means anything. (Its probably a good
    criterion if you're short on disk space).
-   "Lightweight" is a buzzword and meaningless without a reference
    point.
-   Don't judge a library by its size and certainly dont try to draw
    conclusions from the size to the performance, or worse to the
    scalability. It just doesnt work.
-   *Artificial benchmarks suck.*
-   To all the folks hunting for everything lightweight and
    micro-benchmarking all day long: You're wasting your time (Just like
    I did with this stupid benchmark...).
-   Don't trust artificial benchmarks (Not even this one).

PS: This is no post against Outlet, so if any Outlet guys or fans are
reading this, please don't feel offended. Since I dont know Outlet well
I'm sure I did a lot of things wrong but thats really not important
here. I am just making a stance against all the ridiculously stupid
artificial benchmarks out there that try to make people believe Doctrine
is slow and bloated. This post shows I can make it look the other way
around easily. That just shows how meaningless these comparisons are.

> **NOTE** All the code used to run these benchmarks can be downloaded
> from
> [here](http://www.doctrine-project.org/downloads/doctrine2outletbenchmark.zip).
> It is a zip archive containing all the code you need to run the
> benchmarks yourself.
