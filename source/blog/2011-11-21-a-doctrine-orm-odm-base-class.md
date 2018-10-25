---
title: "An ORM/ODM Base Class"
authorName: Benjamin Eberlei
authorEmail:
categories: []
permalink: /2011/11/21/a-doctrine-orm-odm-base-class.html
---
One of most common complaints about Doctrine2 is the requirement to
write getters/setters for all the fields and assocations of every
entity. A concern that immediately follows is that Doctrine 2 is not
suitable for Rapid-Application-Development.

The problem is purely a usability concern and there are a bunch of very
easy ways ouf of this problem:

-   A base-object that has \_\_get/\_\_set.
-   The EntityGenerator can generate getters/setters
-   An IDE that generates getters/setters (Netbeans, PHPStorm)

We have been very critical of ActiveRecord since we started the
development of Doctrine 2 for various reasons. Mostly because we don't
think coupling the database to your domain objects is a good choice for
testability an maintainabilty reasons.

However we do see the need for Doctrine 2 to be suitable for RAD
projects. With the launch of Symfony2 and other frameworks with tight
Doctrine 2 integration this requirement has become even more important.

That is why we will introduce a very lightweight base-class into
Doctrine. We managed to write this base-class on an abstract level
against the Common Metadata interface, such that CouchDB-, MongoDB- and
PHPCR-ODM implementations benefit from this as well.

Using a new hook in Doctrine 2.2-DEV you can now inject the
EntityManager (ObjectManager) and the metadata description into each
entity during construction. This metadata is used to implement the magic
\_\_call hook implementing getters/setters and association management
methods.

Example
=======

A simple example will demonstrate this:

> \<?php use DoctrineCommonPersistencePersistentObject; use
> DoctrineORMEntityManager;
>
> /*\** @Entity
> :   \*\*/
>
> class User extends PersistentObject { /*\* @Id @Column(type="integer")
> @GeneratedValue*\*/ protected \$id;
>
> > /*\* @Column(type="string")*\*/ protected \$name;
> >
> > /*\* @OneToMany(targetEntity="Phonenumber", mappedBy="user")*\*/
> > protected \$phonenumbers;
>
> }

Extending from \`PersistentObject\` will make getters/setters available
for your entities. Bi-directional associations are handled
automatically.

~~~~ {.sourceCode .php}
<?php
/**
 * @Entity
 **/
class Phonenumber extends PersistentObject
{
    /** @Id @Column(type="string") **/
    private $number;

    /** @ManyToOne(targetEntity="User", inversedBy="phonenumbers") **/
    private $user;
}
~~~~

The only configuration call for the \`PersistentObject\` is a
registration of the responsible entity/document manager:

~~~~ {.sourceCode .php}
<?php
$entityManager = EntityManager::create(...);
PersistentObject::setObjectManager($entityManager);
~~~~

You can now start using the entities as simple as this:

~~~~ {.sourceCode .php}
<?php
$number = new Phonenumber();
$number->setNumber(123454);
$user = new User();
$user->setName("Benjamin");
$user->addPhonenumbers($number);

echo $user->getName();
foreach ($user->getPhonenumbers() AS $number) {
    echo $number->getNumber();
}
~~~~

Future Developments
===================

First important notice: We will not develop the \`PersistentObject\`
into a full-fledged active record. Doctrine focuses on being a
DataMapper. We do however provide a bunch of new hooks in version 2.2
that will allow you to turn Doctrine 2 into an active record very
easily:

-   Inject EntityManager and ClassMetadata

If your entity implements \`DoctrineCommonPersistentObjectManagerAware\`
then the ObjectManager and ClassMetadata of the entity will be injected
during construction.

-   EntityManager\#flush() can now flush one entity only

When you pass a single entity to EntityManager\#flush() this entity will
be the only one flushed into the database. Cascade persist rules are
applied to this object. With this feature and access to the
EntityManager inside your entities you can now start implementing an
efficient Active Record with "Record\#save()" and "Record\#delete()"
methods.

-   PHP 5.4 and Traits

The next version of PHP is already in Beta 2 and will probably be
released in the next months. One of the most powerful feature of this
release will be Traits, something very suitable for Doctrine and ORMs in
general.

No worries: Doctrine 2 will always be supporting 5.3, however we will
probably ship with optional features that are using the trait
functionality. High on the list:

-   Porting \`PersistentObject\` to a trait
-   Serialization of entities from a trait (ToArray(), ToJson()).
    Available as a service to PHP 5.3
-   ActiveEntity trait that extends the \`PersistentObject\` one.

Based on this feature set it should even be possible to add behaviors to
Doctrine 2, although we won't focus on implementing behaviors in the
core team.

Last words
==========

I would really appreciate people starting to test the
\`PersistentObject\` if they like too and give us feedback.
