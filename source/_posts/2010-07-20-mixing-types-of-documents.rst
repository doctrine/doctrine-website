---
title: MongoDB ODM: Mixing Types of Documents
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
indexed: false
---
One major advantage to using something like MongoDB is the fact
that it is schema-less. We can store multiple types of documents in
a single collection and we are not limited to a single type of
document in embedded and referenced documents. This article shows
how you can easily mix types of documents in collections, embedded
and referenced documents.

Mixing Types in Collections
---------------------------

If you don't want to use ``SINGLE_COLLECTION`` inheritance you can
easily store different documents in the same collection by using a
discriminator field. First define an ``Article`` document that maps
to ``my_documents``:

.. code-block:: php

    <?php
    /**
     * @Document(collection="my_documents")
     * @DiscriminatorField(fieldName="type")
     * @DiscriminatorMap({"article"="Article", "album"="Album"})
     */
    class Article
    {
        // ...
    }

Next create another document named ``Album`` and also map it to
``my_documents``:

.. code-block:: php

    <?php
    /**
     * @Document(collection="my_documents")
     * @DiscriminatorField(fieldName="type")
     * @DiscriminatorMap({"article"="Article", "album"="Album"})
     */
    class Album
    {
        // ...
    }

Now if you create some instances and persist them they will all be
stored in the ``my_documents`` collection and will have a
discriminator field named ``type`` automatically set with the value
specified in the mapping:

.. code-block:: php

    <?php
    $article = new Article();
    $article->setTitle('Sample Article');
    // ...
    
    $album = new Album();
    $album->setName('My Album');
    
    $dm->persist($article);
    $dm->persist($album);

Finally, if you retrieve the documents they'll all be retrieved
from ``my_documents`` but you will get back the proper PHP classes
that created them:

.. code-block:: php

    <?php
    $articles = $dm->find('Article');
    $albums = $dm->find('Album');

You can retrieve more then just one document type by specifying an
array:

.. code-block:: php

    <?php
    $documents = $dm
        ->createQuery(array('Article', 'Album'))
        ->execute();

The returned documents will contain instances of articles and
albums!

Mixing Types in Embedded Documents
----------------------------------

You can store multiple types of documents in embedded documents by
simply omitting the ``targetDocument`` option. First create a
``User`` document and embed multiple task documents:

.. code-block:: php

    <?php
    /** @Document(collection="users") */
    class User
    {
        // ...
    
        /** @Embedded */
        private $tasks = array();
    
        // ...
    }

    **NOTE** Notice how on the ``$tasks`` annotation we don't specify
    whether it is ``one`` or ``many``. This is because we know it is
    ``many`` due to the default value being an array.


Now create the different types of tasks we can add to the user:

.. code-block:: php

    <?php
    /** @EmbeddedDocument */
    class DownloadTask
    {
        // ...
    }
    
    /** @EmbeddedDocument */
    class UploadTask
    {
        // ...
    }

Now you can embed any type of class in the ``$tasks`` property:

.. code-block:: php

    <?php
    $user = $dm->findOne('User', array(...));
    
    $task = new DownloadTask();
    // ...
    
    $user->addTask($task);
    
    $task = new UploadTask();
    // ...
    
    $user->addTask($task);
    
    $dm->flush();

Mixing Types in Referenced Documents
------------------------------------

Mixing types in referenced documents works just the same as
embedded by omitting the ``targetDocument`` option. In this example
a user can add references to all his favorite albums, songs and
books. First define a ``User`` document with a many references
property for storing the users favorites:

.. code-block:: php

    <?php
    /** @Document(collection="users") */
    class User
    {
        // ...
    
        /** @Reference */
        private $favorites = array();
    
        // ...
    }

Now here is what the referenced documents would look like:

.. code-block:: php

    <?php
    /** @Document(collection="albums") */
    class Album
    {
        // ...
    }
    
    /** @Document(collection="songs") */
    class Song
    {
        // ...
    }
    
    /** @Document(collection="books") */
    class Book
    {
        // ...
    }

Now it is easy to add the references to his favorites:

.. code-block:: php

    <?php
    $user->addFavorite($album);
    $user->addFavorite($song);
    $user->addFavorite($book);
    
    $dm->flush();

When you retrieve the user and access the ``$favorites`` the
documents will be grouped by type and loaded with one or more
``$in`` queries:

.. code-block:: php

    <?php
    $user = $dm->findOne('User', array(...));
    $favorites = $user->getFavorites();
    
    // Lazily loads references
    // Contains Album, Song and Book instances
    foreach ($favorites as $favorite) {
        // ...
    }

That is it! It is easy to take advantage of the schema-less
features of MongoDB with the Doctrine Object Document Mapper
(ODM)!
