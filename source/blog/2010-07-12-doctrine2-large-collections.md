---
title: "Working with Large Collections in Doctrine2"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: beberlei
authorEmail:
categories: []
permalink: /2010/07/12/doctrine2-large-collections.html
---
If you access a collection of Entity A pointing to Entity B, Doctrine2
always initializes the complete collection for you. For small
collections up to around 100 entities this won't be a problem, however
as soon as collections get (much) bigger than this you can get into
serious trouble.

By default Doctrine2 can only optimize adding new entities to a
collection for you. This operation does not initialize the collection.
This will only get you bigger collections though, reading them is still
a pain.

We already got requests from several development teams for better
functionality in this regard and we are planning to add a solution to
this problem that is not constraining your domain model with technical
blurp. However this solution is currently on our schedule for the 2.1
release of Doctrine only.

Until then I wrote a very little extension for Doctrine2 that allows you
to work with large collections. It has two methods that compute the
following data for any given `PersistentCollection`:

-   Total Number of Elements in the Collection
-   A slice of entities from the collection using a sql limit (or
    alternative)

You can get this extension from the [DoctrineExtensions Github
repository](http://github.com/beberlei/DoctrineExtensions).

Working with a LargeCollection
==============================

The `LargeCollection` class is a handler to work with large
PersistentCollections. You can instantiate it by passing an
`EntityManager` instance:

~~~~ {.sourceCode .php}
<?php
use DoctrineExtensions\LargeCollections\LargeCollection;

$lc = new LargeCollection($em);

**NOTE**

LargeCollection only works with instances of
``PersistentCollection``, not with other implementations of the
``Doctrine\Common\Collections\Collection`` interface. That means
that you can only pass collections to it, whose owning entities
have been persisted before or are retrieved from the
EntityManager.
~~~~

You can compute the total number of elements in a given collection by
passing it to the count method:

~~~~ {.sourceCode .php}
<?php
$size = $lc->count($article->getComments());
~~~~

You can retrieve a slice of entities from the collection by calling:

~~~~ {.sourceCode .php}
<?php
$slice = $lc->getSliceQuery($article->getComments(), $limit = 30);
~~~~

As you can see this is very simple to use, but also missing some bits:

-   In your domain models you sometimes don't want to return the
    `Collections` instance but call `toArray()` to encapsulate the
    Collections API inside the Entity. For this two new methods are
    required to access to the persistent collections from the inside of
    an entity.
-   The `remove`, `removeElement`, `contains` and `containsKey` methods
    could also be added to the large collection handler, making direct
    calls to the underlying UnitOfWork API.
-   A method that returns an `IterableResult` for any given collection.
    This would allow to iterate the complete collection on a row-by-row
    basis, which would eliminate possible max memory problems compared
    to the complete hydration of a collection.
-   Methods link()/unlink() like described in
    [DDC-128](http://www.doctrine-project.org/jira/browse/DDC-128)

I hope I got your attention and maybe someone has an interest in
extending the LargeCollection a little bit more.
