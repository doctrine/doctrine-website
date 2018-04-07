---
title: Announcing Doctrine 4
menuSlug: blog
authorName: Maciej Malarz
authorEmail: malarzm@gmail.com
categories: []
permalink: /:year/:month/:day/:basename.html
---
.. note:: 

    This article is an April Fools joke.

It has been few months since we shed some light on the `future of Doctrine project <https://github.com/doctrine/doctrine2/issues/6211>`__
and had an extensive insight into what `Guilherme <https://twitter.com/guilhermeblanco>`__ had been up to. Adding
tens of megabytes of IRC logs of internal discussion we felt we owe you an update on where Doctrine is and where
it's heading to:

Doctrine 3 Is No More!
----------------------

Rest assured, we are in no way ditching all the code Guilherme hacked so far or any of the ideas that sprung
for the next major release. We are still looking into leveraging all goodies that were given to us with PHP 7.
We want the next Doctrine version to be an extremely stable and reliable piece of software. We are also still trying to figure out how to maintain all projects under Doctrine's umbrella effectively. To recap,
Doctrine is not only the ORM you all know, we are also maintaining a number of ODM projects (`MongoDB <https://github.com/doctrine/mongodb-odm>`__
and `CouchDB <https://github.com/doctrine/couchdb-odm>`__ to name few) which all share basic concepts and code.
Some of them also face a major rewrite, like the much anticipated MongoDB ODM 2.0 with support for new MongoDB driver.

Joining Forces
--------------

Instead of having each team work independently and implement the same concepts multiple times across various libraries,
we decided it's for the best if we all work on one project and make it as good and robust as possible.
In the spirit of breaking boundaries, Doctrine 4 will be all about interoperability. **Doctrine 4 will support
both RDBMS and NoSQL databases at the same time!**

Following Latest Trends
-----------------------

A really big thing we want to (re)introduce is an Active Record pattern. We recently ran a poll on the #doctrine IRC
channel and it turned out that 68% of developers die a little bit each time they inject a service, which
barely saves data to a database anyway, and miss the simplicity of having an entity save itself.

.. code-block:: php

    use Doctrine;

    /**
     * @Doctrine\Entity(storage="MSSql")
     * @Doctrine\ActiveRecord
     */
    class User
    {
        /** @Doctrine\Id */
        public $id;

        /** @Doctrine\Field(type="string") */
        public $login;
    }

    $user = new User();
    $user->load(10);
    $user->login = 'malarzm';
    $user->save();

Thanks to the ``@Doctrine\ActiveRecord`` annotation you're able to query for and save your entities easily. Please
notice that the `User` class does not extend any internal Doctrine class - you are still decoupled from the ORM!

We strongly believe that getting back to the Active Record pattern is the way to go for us. We weren't able to
fully get rid of the old fashioned Data Mapper pattern but you can expect its deprecation in one of first bug-fix releases
and full removal in a subsequent patch release (Active Record is replacing semantic versioning, too!)

Another big step towards the highly expected Developer eXperience was initially painful, as it required many
of us to come out of the Java bubble we live in, but we know it will be for the best. With the re-introduction of
Active Record the obvious next step is making all Doctrine utilities available in an easy and sane way: please welcome
static registries AND short method names!

.. code-block:: php

    use Doctrine;
    use Entity\MongoLog;
    use Entity\User;

    Doctrine::em()->start(); // start a transaction
    $user = new User(); // this is stored in MSSql
    $user = 'malarzm';
    $user->save(); // not saved yet
    $log = new MongoLog($user, 'was created'); // this will be "stored" in MongoDB
    $log->save(); // but not yet
    Doctrine::em()->commit(); // commit a transaction ACROSS multiple storage engines

Big shout out goes to `Marco <https://twitter.com/Ocramius>`__. Although he initially had a heart attack when first
hearing about this idea, he's come full circle after using the feature and is now a big proponent of static registries. Be sure
to watch for updates to all of his libraries in the near future!

Try It Out Now!
---------------

Uniting forces of all Doctrine developers has enabled us to ship an usable "alpha" version way sooner than originally
anticipated. But the truly thrilling news is that thanks to tremendous help from guys with `3v4l.org <https://3v4l.org/>`__
we have set up a sandbox environment so everybody can have a hands-on experience using the new version of Doctrine:
please visit `3v4l.org/doctrine4 <https://ocrami.us/>`__ and share your thoughts in the comments section below!
