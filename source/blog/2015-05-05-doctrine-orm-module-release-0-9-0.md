---
title: "DoctrineORMModule release 0.9.0"
authorName: Gianluca Arbezzano
authorEmail: gianarb92@gmail.com
categories: []
permalink: /2015/05/05/doctrine-orm-module-release-0-9-0.html
---
The **Zend Framework Integration Team** is happy to announce the new
release of **DoctrineORMModule**.

DoctrineORMModule 0.9.0 is out of the door!!

*Note* that this is the last version that supports
[doctrine/migrations](https://github.com/doctrine/migrations). We are
working on extracting this feature into an independent module.

Follow issue
[\#401](https://github.com/doctrine/DoctrineORMModule/pull/401).

The Following issues were solved in this release:
:   -   [[\#199] Add 'entity\_listener\_resolver' config
        key](https://github.com/doctrine/DoctrineORMModule/pull/199)
    -   [[\#281] Forced failing unit test for
        \#247](https://github.com/doctrine/DoctrineORMModule/pull/281)
    -   [[\#306] Removing PHP 5.3.3
        support](https://github.com/doctrine/DoctrineORMModule/pull/306)
    -   [[\#272] Fix and test \#270, \#242,
        \#285](https://github.com/doctrine/DoctrineORMModule/pull/272)
    -   [[\#311] remove unused
        statement](https://github.com/doctrine/DoctrineORMModule/pull/311)
    -   [[\#326] Highlight only uppercase
        words](https://github.com/doctrine/DoctrineORMModule/pull/326)
    -   [[\#329] remove unused
        statements](https://github.com/doctrine/DoctrineORMModule/pull/329)
    -   [[\#328] wrong var
        assignment](https://github.com/doctrine/DoctrineORMModule/pull/328)
    -   [[\#313] Prevent overriding of
        type](https://github.com/doctrine/DoctrineORMModule/pull/313)
    -   [[\#338] order the
        classes](https://github.com/doctrine/DoctrineORMModule/pull/338)
    -   [[\#346] Corrected a typo in a comment to better
        clarify](https://github.com/doctrine/DoctrineORMModule/pull/346)
    -   [[\#357] Modify sql\_logger\_collector class
        factory](https://github.com/doctrine/DoctrineORMModule/pull/357)
    -   [[\#360] Add example for
        entity\_resolver](https://github.com/doctrine/DoctrineORMModule/pull/360)
    -   [[\#359] Update deprecated dialog console
        helper](https://github.com/doctrine/DoctrineORMModule/pull/359)
    -   [[\#363] Prevent ZendFormElementFile types inherit of
        StringLength
        validator...](https://github.com/doctrine/DoctrineORMModule/pull/363)
    -   [[\#365] Re-enable scrutinizer code
        coverage](https://github.com/doctrine/DoctrineORMModule/pull/365)
    -   [[\#373] Add doc for
        cache](https://github.com/doctrine/DoctrineORMModule/pull/373)
    -   [[\#347] added extra check in
        handleRequiredField](https://github.com/doctrine/DoctrineORMModule/pull/347)
    -   [[\#377] fix
        docblocks](https://github.com/doctrine/DoctrineORMModule/pull/377)
    -   [[\#376] Add latest migrations
        command](https://github.com/doctrine/DoctrineORMModule/pull/376)
    -   [[\#375] Default
        repository](https://github.com/doctrine/DoctrineORMModule/pull/375)
    -   [[\#374] Use ResolveTargetEntityListener as an event subscriber
        when
        supported](https://github.com/doctrine/DoctrineORMModule/pull/374)
    -   [[\#318] Add support for second level
        cache](https://github.com/doctrine/DoctrineORMModule/pull/318)
    -   [[\#378] Allow to set file lock for
        SLC](https://github.com/doctrine/DoctrineORMModule/pull/378)
    -   [[\#380] Fix typo in configuration file
        markdown.](https://github.com/doctrine/DoctrineORMModule/pull/380)
    -   [[\#385] Allow symfony 3.0
        components](https://github.com/doctrine/DoctrineORMModule/pull/385)
    -   [[\#388] update comment block in Module.php as no
        Module::getAutoloaderConfig()](https://github.com/doctrine/DoctrineORMModule/pull/388)
    -   [[\#389] Delete Module.php in root
        directory](https://github.com/doctrine/DoctrineORMModule/pull/389)
    -   [[\#390] travis: PHP 5.6, 7.0 nightly added, 5.3
        dropped](https://github.com/doctrine/DoctrineORMModule/pull/390)
    -   [[\#392] Use-case: caching module' s
        configuration](https://github.com/doctrine/DoctrineORMModule/pull/392)
    -   [[\#396] Removed unnecessary line in travis
        config](https://github.com/doctrine/DoctrineORMModule/pull/396)
    -   [[\#398] Composer \* -update for stable
        version](https://github.com/doctrine/DoctrineORMModule/pull/398)

To install this version, simply update your `composer.json`:

~~~~ {.sourceCode .json}
{
    "require": {
        "doctrine/doctrine-orm-module": "0.9.0"
    }
}
~~~~
