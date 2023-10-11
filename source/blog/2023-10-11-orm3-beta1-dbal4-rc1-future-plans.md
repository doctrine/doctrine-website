---
title: "ORM 3.0 Beta 1, DBAL 4 RC 1 and future plans"
authorName: Benjamin Eberlei
authorEmail: kontakt@beberlei.de
permalink: /2023/10/11/orm3-beta1-dbal4-rc1-future-plans.html
---

We have released the first beta of the long awaited Doctrine ORM 3 and a
release candidate of DBAL 4. 

The target audience for these releases are framework integration and extension
library authors. ORM 3 is not yet production ready and the APIs may change.

Our goal is to release ORM 3.0 as soon as possible and to gather feedback from
greenfield project authors first. 

This beta release is the result of a lot of work by many contributors,
especially Grégoire, Alexander, Claudio and Matthias on ORM and Sergei on DBAL.
To iron out the final details, we met in Düsseldorf for a Doctrine Core Team
meeting, generously funded by our sponsors through OpenCollective and GitHub.
We also welcomed Matthias as the latest member of the Doctrine Core Team.

### Continued ORM 2 support and forward compatibility

We will maintain the latest branch of the 2 line in ORM for at least another 2
years, possibly longer, to give you enough time to upgrade and us more time to
learn from upgrader feedback and improve forward compatibility.

This means that we will be making ORM 2.x work with newer versions of PHP,
fixing security bugs, and introducing layers and features that help with
forward compatibility in the upgrade path to ORM 3. 

Current users of ORM 2 should note that there is no urgency right now to update
to ORM 3, as we are still working on replacement APIs and forward
compatibility, and do not intend to ship them all with ORM 3.0, but with later
versions.

### ORM 2 users can already prepare for 3 by addressing deprecations

But there is already work to be done as an ORM 2 user: to help you find all the
places where things may be deprecated or changing behaviour, we have created
[the doctrine/deprecations
library](https://github.com/doctrine/deprecations#usage-from-consumer-perspective)
and integrated it heavily into DBAL, ORM and other components.

It allows the use of deprecated behaviour to be logged at runtime with low
overhead, automatic suppression of the same deprecation occurring multiple
times, and a way to ignore selected deprecations for the time being. Each
deprecation message always links to a GitHub issue with more details.

Many deprecated features have no replacement, such as Mapping Exporters,
Generate Mapping from Database, Named Queries. 

For some of the deprecations in ORM, we are still planning replacement APIs,
especially:

* There is currently no way to limit the number of entities that the flush
  operation considers changed. Flush will currently always calculate change
  sets on all entities that are not read-only. 
* As a replacement for removing PARTIAL object hydration, we are looking at
  making embeddable objects lazy, perhaps improving nesting of the new DTO
  expression in DQL. We are also looking to introduce subselect or batch
  loading for collections for more efficient multi-level hydration.

These will be released in 2.x as forward compatible APIs so that you can switch
to using them before upgrading to ORM 3.
