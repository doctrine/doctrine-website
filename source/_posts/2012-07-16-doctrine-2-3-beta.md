---
title: Doctrine 2.3 Beta
menuSlug: blog
authorName: Benjamin Eberlei 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
**16.7.2012**

We tagged the Doctrine 2.3 BETA1 release today. This includes tags for
the Common, DBAL and ORM projects.

This release trys to keep backwards compatibility to every previous
release as much as possible, however some slight changes might be
necessary to your applications. See the UPGRADE files of each project
for details:

-   [ORM](https://github.com/doctrine/doctrine2/blob/master/UPGRADE.md)
-   [DBAL](https://github.com/doctrine/dbal/blob/master/UPGRADE)

This new release contains not single blockbuster feature, but very many
little ones:

-   Custom ID Generators
-   Naming Strategies
-   Collection Criteria API
-   @AssociationOverride and @AttributeOverride (useful for Trait and
    MappedSuperclass)
-   Arbitrary JOIN Syntax (FROM User u JOIN Comment c WITH c.user =
    u.id)
-   Named Native Queries

We will flesh out the documentation and information about all new
features in the coming beta phase. We hope to release the final version
of Doctrine 2.3 just before the upcoming Symfony 2.1 release.

Please test this release with your applications and provide us with
feedback about issues that you find.

You can install the Beta through
[Github](https://github.com/doctrine/doctrine2) or
[Composer](http://www.packagist.org):

    {
        "require": {
            "doctrine/orm": "2.3.0-BETA1"
        }
    }
