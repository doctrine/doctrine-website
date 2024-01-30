---
title: "Archiving Unmaintained Packages"
authorName: Andreas Braun
authorEmail: alcaeus@doctrine-project.org
permalink: /2024/01/30/archiving-unmaintained-packages.md
---

After long consideration, we have decided to archive a number of repositories that have not seen any activity in a
while. This affects the CouchDB and OrientDB ODMs and their respective libraries, as well as the KeyValueStore project.
The following repositories and composer packages are affected:
* [doctrine/couchdb](https://github.com/doctrine/couchdb-client)
* [doctrine/couchdb-odm](https://github.com/doctrine/couchdb-odm)
* [doctrine/couchdb-odm-bundle](https://github.com/doctrine/DoctrineCouchDBBundle)
* [doctrine/orientdb-odm](https://github.com/doctrine/orientdb-odm)
* [doctrine/key-value-store](https://github.com/doctrine/KeyValueStore)

The composer packages will remain available and installable, but we will not be making any bug fixes or security fixes
in the affected libraries. If you or your business depends on one of these libraries, please fork them and maintain them
yourself in the future.
