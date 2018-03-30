---
title: Doctrine 2.1 Beta and Backwards Compatibility Competition
authorName: beberlei 
authorEmail: 
categories: []
indexed: false
---
We would like to announce the first beta release of Doctrine 2.1.
It is packed with new features that will make your life easier:


-  **Indexed associations:** You can force Doctrine to hydrate
   collection elements by using a field of the target entity as key,
   for example the ID or any unique field. See the
   `tutorial for this feature <http://www.doctrine-project.org/docs/orm/2.0/en/tutorials/working-with-indexed-associations.html>`_.
-  **Extra Lazy Collections:** Instead of always initializing the
   complete collection in memory you can now mark a collection as
   extra lazy, leading to special SQL executed for Collection#count(),
   Collection#contains() and Collection#slice(). This allows to
   implement efficient pagination on collections without having to use
   DQL. It also allows to save some memory for common use-cases with
   very large collections. See the
   `tutorial for this feature <http://www.doctrine-project.org/docs/orm/2.0/en/tutorials/extra-lazy-associations.html>`_.
-  **Identity through Foreign Entities or derived entities:** You
   can now use a foreign key as identifier of an entity. This
   translates to using @Id on a @ManyToOne or @OneToOne association.
   You can read up on this
   `feature in the tutorial <http://www.doctrine-project.org/docs/orm/2.0/en/tutorials/composite-primary-keys.html#identity-through-foreign-entities>`_.
-  **Persister Refactoring:** Instead of reimplementing hydration
   in the persisters we now use the hydration mechanism that is used
   by DQL aswell. Sadly performance for hydration in the persisters
   drops by 5-25% for different use-cases. It starts with a drop of 5%
   for a few hydrations and increases the more hydrations you are
   doing in a request. As a benefit we could remove tons of code and
   use several optimizations that actually increase performance when
   using fetch="EAGER" in ManyToOne and OneToOne associations.
   Furthermore inverse OneToOne associations previously always
   executed an additional query, which is now replaced with a join.
-  **Temporary fetch mode in DQL** On a DQL Query you can now call
   `$query->setFetchMode($`className, $assocName, $fetchMode) to
   temporarily set the fetch mode to a value different from the one
   defined in the Association Mapping. If you set a ManyToOne or
   OneToOne association to eager fetching Doctrine will use a batch
   WHERE id IN (..) query to fetch all entities in a single query.
-  **Binding Arrays to a Query:** Doctrine now implements low-level
   support for binding arrays to named or positional parameters. This
   is possible with the Doctrine::TYPE\_INT\_ARRAY and
   Doctrine::TYPE\_STR\_ARRAY parameters that you have to pass as
   types to a query you want to use this feature in. EntityRepository
   now supports passing arrays as values to a field and uses an IN
   query.
-  **EntityRepository Limit and OrderBy:** The method
   EntityRepository#findBy() now accepts additional parameters for
   ordering, limit and offset.
-  **ResultSetMapping Helper:** There is now a class that
   simplifies populating a ResultSetMapping based on an existing
   ClassMetadata instance.
-  **Zero Based Parameters in Queries:** You can now start with the
   parameter ?0 in DQL queries.
-  **Named DQL Queries in Metadata:** You can add dql queries in
   the mapping files using @NamedQueries(@NamedQuery(name="foo",
   query="DQL")) and access them through
   $em->getRepository()->getNamedQuery().
-  **Date related DQL functions:** Suport for DATE\_ADD(),
   DATE\_SUB() and DATE\_DIFF() in DQL.
-  **New console command orm:info:** Gives details about all
   registered entities and if their mappings are valid or not.
-  **Read Only Entities:** You can set the attribute readOnly=true
   on an entity. This will only allow to persist new instances of this
   entity or removing them, they will never be considered for
   updating, thus allowing for performance optimizations where these
   entities are not considered in the UnitOfWork changeset
   computations.
-  **SQL Query Object:** There is now an SQL Query object in the
   Doctrine project. You can create an instance with
   $connection->createQueryBuilder().
-  **Automatic Parameter Type Inference:** For certain parameters
   types such as integer and DateTime ORM Query::setParameter can now
   automatically infer the type instead of requiring manually passing
   the values as third parameter.

Documentation for all the feature will be updated in the next
weeks. The release is planned for June 30th 2011.

With all this new features, some of them requiring large internal
refactorings, we want to assure that Doctrine 2.1 is backwards
compatible with Doctrine 2.0. Our testsuite ensuring backwards
compatibility is very large, but we cannot be sure that we test for
every edge case. That is where you as Doctrine-user come into play:
Please test Doctrine 2.1 with your applications and give us
feedback about backwards compatibility. Please report any problem
on Jira or write a mail to the doctrine-user or doctrine-dev
mailing list.

Anyone finding a backwards incompatible change gets an honorable
mention in the release notes and some may even get small presents!
(This only applies to versions >= Doctrine 2.0.0 with no
customizations and people living in countries with reasonable
shipping rates :-)).
