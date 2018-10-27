---
title: "Our HHVM Roadmap"
authorName: Benjamin Eberlei
authorEmail:
categories: []
permalink: /2013/12/23/our-hhvm-roadmap.html
---
Facebook has been [pushing HHVM alot
lately](http://www.hhvm.com/blog/2813/we-are-the-98-5-and-the-16) ,
helping open source projects to get their test-suite running 100%. For
Doctrine HHVM is particularly interesting, because of the performance
gains that the complex PHP algorithms inside ORM would probably get.
From my current feeling Doctrine will be the PHP open-source project
getting the most gain from running on HHVM. However with the tests not
yet passing on the ORM, we can only imagine how big that performance
improvement will be.

One roadblock for us to investigate HHVM in more detail was missing CI
support. But then Travis CI [announced support for
HHVM](http://about.travis-ci.org/blog/2013-12-16-test-php-code-with-the-hiphop-vm)
last week. With automated testing support available we think it is time
to announce our official HHVM roadmap.

One of our goals for 2014 is running DBAL and ORM on HHVM with 100% of
the testsuites passing. Every Doctrine subproject targeting to support
HHVM will start running the tests against HHVM with `allow_failure`
enabled on Travis CI. Whenever a Doctrine subproject passes all its
tests on HHVM, we will remove the `allow_failure` and the project will
be officially supporting HHVM from that version on.

So far we have been working on the Common projects to run on HHVM for
several months now and Guilherme and Alexander are contributing to HHVM
itself to get some missing APIs working. We are happy to announce that
the following Common projects currently have full HHVM support from us:

-   [Collections](https://travis-ci.org/doctrine/collections)
-   [Inflector](https://travis-ci.org/doctrine/inflector)
-   [Lexer](https://travis-ci.org/doctrine/lexer)

Guilherme is working on getting
[Annotations](https://travis-ci.org/doctrine/annotations) and
[Cache](https://travis-ci.org/doctrine/cache) working and the
[Common](https://travis-ci.org/doctrine/common) mainproject will be
evaluated shortly after all the common projects succeed.
[DBAL](https://travis-ci.org/doctrine/dbal) and
[ORM](https://travis-ci.org/doctrine/doctrine2) will be much more work,
but we are very confident to achieve this goal.

If you want to help us with this goal, you can check the current Travis
failure reports of the projects and come up with ideas how to fix them
in the Doctrine code or with bug reports for HHVM. We are glad to
discuss these issues on Freenode IRC in channel "\#doctrine-dev".

With this announcement we hope that other PHP projects, frameworks and
libraries will follow to make HHVM an official build target in the
future.
