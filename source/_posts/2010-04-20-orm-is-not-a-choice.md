---
title: ORM is Not a Choice
menuSlug: blog
authorName: romanb 
authorEmail: 
categories: []
permalink: /2010/04/20/orm-is-not-a-choice.html
---
**NOTE** When speaking of "ORM" or "object-relational mapping" in
:   this post I am referring to the act of mapping an [object-oriented
    domain model](http://martinfowler.com/eaaCatalog/domainModel.html)
    to a relational database. There are other, alternative forms of
    object-relational mapping.

"Should I use an ORM?" is a frequently asked question that somehow
misses the point, because ORM is usually not some optional thing you can
either use or not. The choices are elsewhere. Furthermore, if there is a
dislike for ORM tools it helps to clarify what exactly is the cause. The
cause can be a dislike of object-oriented domain models. For example, if
you prefer to separate data from behavior/logic, as [I've read recently
on Twitter](http://twitter.com/elazar/status/12492601691) , then it is a
sign that you don't like domain models, at least not rich ones, maybe
[anemic ones](http://martinfowler.com/bliki/AnemicDomainModel.html) ,
and you probably don't like OOP much at all then, because bundling data
with behavior is what OO is about, usually.

> **NOTE** It's OK not to like OOP these days. Its not the holy grail
> anymore, mostly due to the usually messy concurrency characteristics
> and problems resulting from typical object-oriented design which
> revolves around imperative programming with direct manipulation of
> mutable state, but that does not apply to PHP as much as to some other
> languages due to its thread-confined nature/execution model.

You can not use a relational database in combination with an
object-oriented domain model without mapping.

The choices that lead to the need for ORM are the following:

-   *You can choose to use a relational database or not.*
-   *You can choose to create an object-oriented domain model or not.*

If you want a relational database and you want an object-oriented domain
model, you need ORM, there is no choice. If you choose to use a
relational database but not an object-oriented domain model, you might
need some other form of object-relational or other mapping, depending on
how you want to model your application and your business logic around
the data in particular.

People not being aware of the above two choices, especially the second
one, is unfortunately what makes some of them "choose ORM" and then
sometimes getting frustrated. Using an ORM without even having or
wanting a domain model and with the head still exclusively full of
tables and rows, which are at the beginning and at the end of every
thought about the software being built, is a wrong choice. With these
preconditions, ORM quickly becomes a pain, and its wrong. Forcing your
relational data into objects even though you don't really know what to
do with them, maybe it just seems nice to have them as "data
containers", is the wrong motivation. If you don't want to combine your
business data with your business behavior, something that can be done
nicely in an OO domain model, then there is not much value in having
objects wrapped around your data. Some other, wrong motivations for
"choosing an ORM", at least when they are the main motivations, are:

-   Database vendor independence
-   "Hiding SQL"

These are just additional benefits you can get but they are not the main
purpose of an ORM tool. Then what is the main purpose? *State
management. Synchronizing the state of your in-memory object model with
a relational database for the purpose of persistent storage.* Neither
does an ORM need to be database vendor independent, nor does it need to
hide SQL to do this. Everything else is icing on the cake. If all you
want is to centralize your database access paths, which is a good thing
for caching and all, you don't need an ORM for that either, any database
access layer, existing or self-made, will do.

I hope it is a bit clearer now that ORM is not a choice but a need that
results out of other choices.
