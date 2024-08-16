---
title: "The skeletons are now out of the closet - So long, Skeleton-Mapper"
authorName: Claudio Zizza
authorEmail: claudio@doctrine-project.org
permalink: /2024/08/16/the-skeletons-are-now-out-of-the-closet.html
---

While Doctrine ORM and DBAL have a main focus in our daily development, a deeper look 
into their dependencies show, that Doctrine has much more projects at hand than just the 
database-related ones. Some projects even weren't created for ORM or DBAL, just like 
our Skeleton-Mapper.

The [Skeleton-Mapper project](https://github.com/doctrine/skeleton-mapper) won't be maintained anymore because of 
its lack of usage nowadays and is now an archived repository. The Doctrine Skeleton-Mapper 
was an object mapper where you are responsible for implementing the object mapping of the 
persistence operations. This means you write plain old PHP code for the data repositories, 
object repositories, object hydrators and object persisters. A lot of freedom but also a lot 
of work for a developer, including its maintenance for us.

Some projects grow and others become obsolete after some time, which are 9 years in 
the case of our Skeleton-Mapper. We also want to express our gratitude to 
[the contributors](https://github.com/doctrine/skeleton-mapper/graphs/contributors) 
and maintainers who kept this project alive for so long. Thank you.
