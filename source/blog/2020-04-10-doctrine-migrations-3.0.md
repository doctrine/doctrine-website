---
title: "Released doctrine/migrations 3.0-alpha"
authorName: Asmir Mustafic
authorEmail: goetas@gmail.com
permalink: /2020/04/10/doctrine-migrations-3.0.html
canonical: https://www.goetas.com/blog/released-doctrinemigrations-30-alpha/
---

[`doctrine/migrations` 3.0-alpha](https://github.com/doctrine/migrations) 
 has been [published](https://github.com/doctrine/migrations/tree/3.0.0-alpha1) on the 29th March 2020. 

The upcoming 3.0 new major release is the result of almost 6 months of work and brings
 a completely refactored/rewritten internal structure and some interesting new features.  
 
### Why a new major release?

The `doctrine/migrations` `v1.x` codebase is 10 years old, and in the past years a lot of features have been added on top of its
initial architecture.   
`doctrine/migrations` `2.0` was released a bit more than a year ago. This major release did a bit of cleanup, 
but the general structure remained the same.
In this schema you can see the dependencies between classes in the latest `2.3.x` branch:

<div class="text-center">
<object width="70%" data="/images/posts/doctrine-migrations-3.0/complex-cycle-v2.svg" type="image/svg+xml"></object>
</div>
The red lines are circular dependencies (and we already know that in software development circular dependencies are not 
a good thing).

In `doctrine/migrations` `3.x`, most of the internal classes have been re-written and dependency injection has been 
widely adopted.  
In this schema you can see the dependencies between classes in the latest `master` branch (release v3.0):

<div class="text-center">
<object width="70%" data="/images/posts/doctrine-migrations-3.0/complex-cycle-v3.svg" type="image/svg+xml"></object>
</div>

As you can see the circular dependencies are gone. This has been possible thanks to extensive use of dependency injection
and applying SOLID principles.
To reduce future backward incompatibilities, many classes have been marked as `final` or as `@internal` while 
keeping the functionalities intact. Extensibility is still possible by using dependency injection and providing 
classes implementing dedicated interfaces.

_These schemas have been generated using [PhpDependencyAnalysis](https://github.com/mamuz/PhpDependencyAnalysis) 
with [this configuration](https://gist.github.com/goetas/e6343746a6ccd6ebb191cbbd675898e0)._


### New features and improvements

Beside the code quality improvements, there is a a long list of improvements (see below), but 
**the main user-facing feature is the ability to collect migrations from multiple folders/namespaces and
to specify dependencies between migrations.**

Here a (probably not complete) list of improvements implemented in the upcoming `3.0` release: 

- ability to collect migrations from multiple folders/namespaces and to specify dependencies between migrations
- `doctrine/migrations` will write to your database only when running migrations 
(previously the metadata table was created on the very first command run even if it was a read-only command)
- Output verbosity can be controlled using the `-v`, `-vv` or `-vvv` command parameters
- Use of dependency injection allows you to decorate/replace most services
- Removed usage of console helpers to provide the connection or entity manager in favor of dependency injection
- Introduced `migrations:list` command to list the available/executed migrations
- Introduced `migrations:sync-metadata-storage` command to explicitly update the metadata schema in case a newer version 
introduces changes to the metadata table
- Multiple migrations can be passed to the `migrations:execute` command
- More organized output of the `migrations:status` command
- Configurations and Dependency Factory are read-only during the migration process
- The `down()` migration is optional now
- Multi-namespace migrations
- Custom migrations metadata storage
- Added warning when using the `migrations:diff` if there are not executed migrations

### Backward compatibility

In `doctrine/migrations` `3.0` a lot of things changed, but for end-users most of the things will look the same.
Your migration files do not need any update.

You will have to change  your configuration files, as the configuration format has changed.
The [official documentation](https://www.doctrine-project.org/projects/doctrine-migrations/en/latest/reference/configuration.html#configuration) contains more information about these changes.
This documentation should be particularly helpful if you did also some custom integration with third party frameworks 
or libraries.

If you wrote custom event listeners, please take a look at them as the method signatures for event listeners have been updated.
 

### Symfony Integration

If you are using [DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle) then things are even 
easier: the 2.3.0 release introduced some deprecation notices and if you have already solved them 
your configuration is already compatible. If you want you can have a look to the latest configuration format 
available on the [official documentation](https://www.doctrine-project.org/projects/doctrine-migrations-bundle/en/3.0/index.html#configuration).
You can look more in detail to which changes are needed in the [upgrading](https://github.com/doctrine/DoctrineMigrationsBundle/blob/master/UPGRADE.md) document.

### What is next

In the upcoming weeks, we will be preparing the first beta release and starting the process to reach a stable release.
**To be able to deliver a good stable release it is important that you test the pre-release and share your feedback!**

To try the alpha version, you can run:

```bash
composer require 'doctrine/migrations:^3.0@alpha'
```  

If you are using Symfony: 

```bash
composer require 'doctrine/doctrine-migrations-bundle:^3.0@alpha' 'doctrine/migrations:^3.0@alpha'
```  

You can be also more brave trying the development versions by specifying `@dev` instead of `@alpha` 
when requiring the composer dependencies above.

You can also have a look at the [release notes](https://github.com/doctrine/migrations/releases/tag/3.0.0-alpha1) 
and the [upgrading](https://github.com/doctrine/migrations/blob/3.0.0-alpha1/UPGRADE.md) document.

Similarly you can also have a look at the [release notes](https://github.com/doctrine/DoctrineMigrationsBundle/releases/tag/3.0.0-alpha.1) 
and the [upgrading](https://github.com/doctrine/DoctrineMigrationsBundle/blob/3.0.0-alpha.1/UPGRADE.md) document for the Symfony bundle.

In the alpha release, breaking changes are still possible. 
In the beta, release breaking changes are possible but will happen
only if we will find very unexpected behaviors. 
When the alpha and beta phase will be completed, a stable version will be made available. 


## Note

This post was initially published on [https://www.goetas.com/blog/released-doctrinemigrations-30-alpha/](https://www.goetas.com/blog/released-doctrinemigrations-30-alpha/).
