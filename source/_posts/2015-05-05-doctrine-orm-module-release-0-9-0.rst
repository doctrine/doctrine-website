---
title: DoctrineORMModule release 0.9.0
menuSlug: blog
authorName: Gianluca Arbezzano
authorEmail: gianarb92@gmail.com
categories: []
permalink: /:year/:month/:day/:basename.html
---
The **Zend Framework Integration Team** is happy to announce the new release of **DoctrineORMModule**.

DoctrineORMModule 0.9.0 is out of the door!!

*Note* that this is the last version that supports `doctrine/migrations <https://github.com/doctrine/migrations>`_. We are working on extracting this feature into an independent module.

Follow issue `#401 <https://github.com/doctrine/DoctrineORMModule/pull/401>`_.

The Following issues were solved in this release:
 - `[#199] Add 'entity_listener_resolver' config key <https://github.com/doctrine/DoctrineORMModule/pull/199>`_
 - `[#281] Forced failing unit test for #247 <https://github.com/doctrine/DoctrineORMModule/pull/281>`_
 - `[#306] Removing PHP 5.3.3 support <https://github.com/doctrine/DoctrineORMModule/pull/306>`_
 - `[#272] Fix and test #270, #242, #285 <https://github.com/doctrine/DoctrineORMModule/pull/272>`_
 - `[#311] remove unused statement <https://github.com/doctrine/DoctrineORMModule/pull/311>`_
 - `[#326] Highlight only uppercase words <https://github.com/doctrine/DoctrineORMModule/pull/326>`_
 - `[#329] remove unused statements <https://github.com/doctrine/DoctrineORMModule/pull/329>`_
 - `[#328] wrong var assignment <https://github.com/doctrine/DoctrineORMModule/pull/328>`_
 - `[#313] Prevent overriding of type <https://github.com/doctrine/DoctrineORMModule/pull/313>`_
 - `[#338] order the classes <https://github.com/doctrine/DoctrineORMModule/pull/338>`_
 - `[#346] Corrected a typo in a comment to better clarify <https://github.com/doctrine/DoctrineORMModule/pull/346>`_
 - `[#357] Modify sql_logger_collector class factory <https://github.com/doctrine/DoctrineORMModule/pull/357>`_
 - `[#360] Add example for entity_resolver <https://github.com/doctrine/DoctrineORMModule/pull/360>`_
 - `[#359] Update deprecated dialog console helper <https://github.com/doctrine/DoctrineORMModule/pull/359>`_
 - `[#363] Prevent Zend\Form\Element\File types inherit of StringLength validator... <https://github.com/doctrine/DoctrineORMModule/pull/363>`_
 - `[#365] Re-enable scrutinizer code coverage <https://github.com/doctrine/DoctrineORMModule/pull/365>`_
 - `[#373] Add doc for cache <https://github.com/doctrine/DoctrineORMModule/pull/373>`_
 - `[#347] added extra check in handleRequiredField <https://github.com/doctrine/DoctrineORMModule/pull/347>`_
 - `[#377] fix docblocks <https://github.com/doctrine/DoctrineORMModule/pull/377>`_
 - `[#376] Add latest migrations command <https://github.com/doctrine/DoctrineORMModule/pull/376>`_
 - `[#375] Default repository <https://github.com/doctrine/DoctrineORMModule/pull/375>`_
 - `[#374] Use ResolveTargetEntityListener as an event subscriber when supported <https://github.com/doctrine/DoctrineORMModule/pull/374>`_
 - `[#318] Add support for second level cache <https://github.com/doctrine/DoctrineORMModule/pull/318>`_
 - `[#378] Allow to set file lock for SLC <https://github.com/doctrine/DoctrineORMModule/pull/378>`_
 - `[#380] Fix typo in configuration file markdown. <https://github.com/doctrine/DoctrineORMModule/pull/380>`_
 - `[#385] Allow symfony 3.0 components <https://github.com/doctrine/DoctrineORMModule/pull/385>`_
 - `[#388] update comment block in Module.php as no Module::getAutoloaderConfig()  <https://github.com/doctrine/DoctrineORMModule/pull/388>`_
 - `[#389] Delete Module.php in root directory <https://github.com/doctrine/DoctrineORMModule/pull/389>`_
 - `[#390] travis: PHP 5.6, 7.0 nightly added, 5.3 dropped <https://github.com/doctrine/DoctrineORMModule/pull/390>`_
 - `[#392] Use-case: caching module' s configuration <https://github.com/doctrine/DoctrineORMModule/pull/392>`_
 - `[#396] Removed unnecessary line in travis config <https://github.com/doctrine/DoctrineORMModule/pull/396>`_
 - `[#398] Composer * -update for stable version <https://github.com/doctrine/DoctrineORMModule/pull/398>`_

To install this version, simply update your ``composer.json``:

.. code-block:: json

  {
      "require": {
          "doctrine/doctrine-orm-module": "0.9.0"
      }
  }
