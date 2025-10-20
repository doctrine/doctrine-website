---
title: "New in MongoDB ODM: Queryable Encryption and Vector Search"
authorName: Jérôme Tamarelle
authorEmail: jerome.tamarelle@mongodb.com
permalink: /2025/10/20/mongodb-odm-release-vector-encryption.html
---

We are pleased to announce the releases of Doctrine MongoDB ODM 2.12.0 and 2.13.0, which introduce two major new capabilities: [Queryable Encryption](https://www.mongodb.com/docs/manual/core/queryable-encryption) and [Vector Search](https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-overview).

- Version 2.12.0 adds support for Client-Side and Queryable Encryption – [View the release notes](https://github.com/doctrine/mongodb-odm/releases/tag/2.12.0)
- Version 2.13.0 introduces Vector Search and a new Vector field type – [View the release notes](https://github.com/doctrine/mongodb-odm/releases/tag/2.13.0)

Queryable and Client-Side Encryption
====================================

Version 2.12 integrates support for MongoDB Queryable Encryption and Client-Side Field Level Encryption (CSFLE). These features allow applications to encrypt sensitive fields before sending data to the database, while still supporting equality and range queries on those encrypted fields. The ODM now provides direct integration with these encryption capabilities, allowing encrypted fields to be defined within document mappings. This enables applications to handle confidential data such as personal or financial information securely without sacrificing query functionality. For details and examples, see the [Encryption documentation of the Doctrine MongoDB Bundle](https://www.doctrine-project.org/projects/doctrine-mongodb-bundle/en/5.5/encryption.html) or the FreeCodeCamp tutorial to [Build Secure Web Applications with PHP, Symfony, and MongoDB](https://www.youtube.com/watch?v=UuknxVdqzb4)

Vector Search
=============

Version 2.13 adds Vector Search, enabling semantic and similarity queries directly through the ODM. It introduces new vector field types and support for the `$vectorSearch` aggregation stage. With Vector Search, you can store and query embedding vectors in MongoDB, making it easier to build semantic search, recommendation, and AI-driven retrieval applications without a separate vector database. This feature integrates cleanly with the ODM aggregation builder, making vector-based queries straightforward to implement. Learn more in the [Vector Search Cookbook](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/2.13/cookbook/vector-search.html).

Full-Text Search
================

The ODM also supports MongoDB Atlas Search, the built-in full-text search solution that operates directly within MongoDB Atlas. Atlas Search provides powerful full-text, relevance, and vector-based search capabilities using indexes that are automatically updated from your collections. This eliminates the need for a separate search engine or an external indexing pipeline.

Vector and full-text search features rely on MongoDB Atlas, but they can also be tested and run locally or in continuous integration environments using a [Local Atlas Deployment](https://www.mongodb.com/docs/atlas/cli/current/atlas-cli-deploy-local/). This makes it possible to develop and test Atlas Search and Vector Search functionality without requiring a cloud deployment.

For more details on how to integrate Atlas Search in your project, see the [Simple Search Engine Cookbook](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/2.13/cookbook/simple-search-engine.html).

With the addition of Queryable Encryption and Vector Search, ODM now supports three advanced capabilities in a single stack:

- Full-text search with Atlas Search
- Semantic similarity search with Vector Search
- Secure queryable storage with Client-Side Encryption

Upgrading
=========

New minor releases in the 2.x branch are backward-compatible. Before using new features, make sure your MongoDB server and PHP driver versions support these features. Queryable Encryption requires MongoDB 7.0 or later with client-side encryption configured, and Vector Search requires MongoDB 6.0.11 or later (or MongoDB Atlas with vector search enabled). Full details are available in the [MongoDB ODM release notes](https://github.com/doctrine/mongodb-odm/releases).

Acknowledgements
================

MongoDB Inc. is committed to the Doctrine project and supports it by providing engineers who work alongside community contributors to maintain and evolve the ODM. This collaboration ensures that the library continues to support the latest MongoDB features and remains a reliable and modern tool for PHP developers.

Doctrine MongoDB ODM continues to evolve with the MongoDB ecosystem, bringing together security, full-text search, and semantic capabilities in one consistent data layer.
