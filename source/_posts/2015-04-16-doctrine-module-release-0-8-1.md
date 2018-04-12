---
title: Doctrine Module release 0.8.1
menuSlug: blog
authorName: Gianluca Arbezzano
authorEmail: gianarb92@gmail.com
categories: []
permalink: /2015/04/16/doctrine-module-release-0-8-1.html
---
The **Zend Framework Integration Team** is happy to announce the new
release of **DoctrineModule**. DoctrineModule `0.8.1` will be the last
bugfix, it is the last version that will support PHP 5.3. Further
versions of the `0.8.*` series may still be released in case of security
issues.

Following issues were solved in this release:
:   -   [[\#376] Bumping PHP and ZF2 dependencies, branch alias for
        master](https://github.com/doctrine/DoctrineModule/pull/376)
    -   [[\#378] I think this is a small PSR rule
        violation.](https://github.com/doctrine/DoctrineModule/pull/378)
    -   [[\#381] Update
        validator.md](https://github.com/doctrine/DoctrineModule/pull/381)
    -   [[\#388] Added exception for missing required parameter for
        find\_method option
        as](https://github.com/doctrine/DoctrineModule/pull/388)
    -   [[\#390] Clarified how to pass sort
        information.](https://github.com/doctrine/DoctrineModule/pull/390)
    -   [[\#395] Issue with objects being cast to array in
        validators](https://github.com/doctrine/DoctrineModule/pull/395)
    -   [[\#397] Enhancement: use exit code from
        run()](https://github.com/doctrine/DoctrineModule/pull/397)
    -   [[\#401] Reading
        Inconsistency](https://github.com/doctrine/DoctrineModule/pull/401)
    -   [[\#391] UniqueObject Validator \* allowing composite
        identifiers from context or
        not](https://github.com/doctrine/DoctrineModule/pull/391)
    -   [[\#400] let zf2 console return exit
        status](https://github.com/doctrine/DoctrineModule/pull/400)
    -   [[\#404] Fix form
        elements](https://github.com/doctrine/DoctrineModule/pull/404)
    -   [[\#406] Fix context
        unique](https://github.com/doctrine/DoctrineModule/pull/406)
    -   [[\#421] Make DoctrineObject use AbstractHydrator s
        namingStrategy](https://github.com/doctrine/DoctrineModule/pull/421)
    -   [[\#426] update year in
        license](https://github.com/doctrine/DoctrineModule/pull/426)
    -   [[\#436] Fixing typo and updating paginator link to ZF
        2.3](https://github.com/doctrine/DoctrineModule/pull/436)
    -   [[\#450] minor cs
        fix](https://github.com/doctrine/DoctrineModule/pull/450)
    -   [[\#458] Update
        doctrine\*module.php](https://github.com/doctrine/DoctrineModule/pull/458)
    -   [[\#462] Adding custom Doctrine\*Cli
        Commands](https://github.com/doctrine/DoctrineModule/pull/462)
    -   [[\#465] Re*enable scrutinizer*ci code
        coverage](https://github.com/doctrine/DoctrineModule/pull/465)
    -   [[\#453] phpdoc
        fixes](https://github.com/doctrine/DoctrineModule/pull/453)

To install this version, simply update your \`composer.json\`:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/doctrine-module": "0.8.1"
    }
}
~~~~
