---
title: Doctrine 2.0 Quality Assurance
authorName: guilhermeblanco 
authorEmail: 
categories: []
indexed: false
---
Greetings folks!

Today I'd like to talk about Quality Assurance in PHP projects.
Currently, PHP lacks good tools for QA, but thanks to a special PHP
user, `Sebastian Bergmann <http://sebastian-bergmann.de>`_ , this is
changing gradually. If you don't know him, you can visit his blog
and check about projects he's on. For lazy people, he's the author
of `PHPUnit <http://www.phpunit.de>`_ , a de-facto Unit Test suite
in PHP.

Doctrine 2.0 uses PHPUnit as our Unit Test suite. It relies on PEAR
to be installed, but you can also install it via SVN.

The main initiative of QA in PHP projects is the website
`http://qualityassuranceinphpprojects.com <http://qualityassuranceinphpprojects.com>`_.
There you can find available tools to measure, for example, how
complex is your project and possible semantical issues.

I have applied some of these tests against
`Doctrine 2.0 <http://trac.doctrine-project.org/browser/trunk>`_ to
see how complex it is and if it can be optimized more. The first
test I run is a trace about how complex our code base is. The tool
I used is
`phploc <http://github.com/sebastianbergmann/phploc/tree/master>`_.
Check out the results:

::

    [bash]
    MacBlanco:bin guilhermeblanco$ ./phploc /Users/guilhermeblanco/www/doctrine/trunk/lib
    phploc 1.1.1 by Sebastian Bergmann.
    
    Directories:                               31
    Files:                                    210
    
    Lines of Code (LOC):                    38826
    Comment Lines of Code (CLOC):           17004
    Non-Comment Lines of Code (NCLOC):      21822
    
    Interfaces:                                11
    Classes:                                  229
    Non-Static Methods:                      1699
    Static Methods:                           106
    Functions:                                 95

Of course it still misses a couple of code to implement (CLI Tasks,
Locking strategies, ID Generators), but now we know how big
Doctrine 2.0 is. Then, I decided to check duplicated code (possible
optimization locations). The tool
`phpcpd <http://github.com/sebastianbergmann/phpcpd/tree/master>`_
gave me this feedback:

::

    [bash]
    MacBlanco:bin guilhermeblanco$ ./phpcpd /Users/guilhermeblanco/www/doctrine/trunk/lib
    phpcpd 1.1.1 by Sebastian Bergmann.
    
    Found 1 exact clones with 15 duplicated lines in 2 files:
    
      - ./Doctrine/DBAL/Platforms/MsSqlPlatform.php:126-141
        ./Doctrine/DBAL/Platforms/MySqlPlatform.php:633-648
    
    0.04% duplicated lines out of 38826 total lines of code.

I asked myself: It must be a method that could be moved to
``AbstractPlatform.php``! So I opened both files and... no! It's a
piece of code that cannot be optimized. So, consider Doctrine 2.0
extremely optimized, because there is no duplicated code
internally!

Now Unit Test suite. Doctrine 2.0 has a steadily growing set of
Unit Tests, and we are regularly analyzing the code coverage
analysis to find parts that need more tests. Here is how such a
coverage report is generated:

::

    [bash]
    MacBlanco:tests guilhermeblanco$ phpunit --coverage-html=./_coverage Doctrine_Tests_AllTests
    PHPUnit 3.3.17 by Sebastian Bergmann.
    
    .....................................S.S....................  60 / 562
    ............................................................ 120 / 562
    .....................................................SSSSSSS 180 / 562
    SSSSSSSSSSSSSSSS............................................ 240 / 562
    ............................................................ 300 / 562
    ............................................................ 360 / 562
    ............................................................ 420 / 562
    ............................................................ 480 / 562
    ............................................................ 540 / 562
    ...SS.................
    
    Time: 18 seconds
    
    OK, but incomplete or skipped tests!
    Tests: 562, Assertions: 1420, Skipped: 27.
    
    Generating code coverage report, this may take a moment.

The generated coverage can be seen in the following picture.

Finally, some metrics are good to inspect how stable is our code. I
applied `pdepend <http://pdepend.org>`_ , and it gave me these
results:

.. figure:: http://www.doctrine-project.org/blog-images/doctrine-2-0-qa/picture2.png
   :align: center
   :alt: jdepend chart
   
   jdepend chart

.. figure:: http://www.doctrine-project.org/blog-images/doctrine-2-0-qa/picture3.png
   :align: center
   :alt: pyramid overview
   
   pyramid overview

The command I ran was:

::

    [bash]
    MacBlanco:bin guilhermeblanco$ ./pdepend --summary-xml=/Users/guilhermeblanco/summary.xml --jdepend-chart=/Users/guilhermeblanco/jdepend.svg --overview-pyramid=/Users/guilhermeblanco/pyramid.svg /Users/guilhermeblanco/www/doctrine/trunk/lib

Here is the generated summary. If you have any other ideas/tools
that we should apply in our codebase to generate other metrics,
please drop us a message!
