---
title: "PHPCR ODM QueryBuilder v2"
menuSlug: blog
layout: blog-post
authorName: dantleech
authorEmail:
categories: []
permalink: /2013/09/25/phpcr-odm-qbv2.html
---
managed to merge the new query builder.

I developed the original query builder about 9 months ago - it was one
of my first contributions to the PHPCR-ODM doctrine project. It was
heavily based on the ORM query builder and the consensus being that we
should make the ODM as intuitive as possible for existing ORM users.

Abstracting PHPCR to fit the existing interface worked up until a point,
we could implement the *basic* functionality of the ORM Query Builder
exactly, things started to come undone when we looked at implementing
joins.

I had added the joins in the API but didn't get around to implementing
them, the methods just threw "not implemented" exceptions. Later, when
we wanted to implement them, it wasn't so simple. Infact, upon closer
inspection, many of the things available in the PHPCR API's Query Object
Model interface were not covered by the model of the query builder we
had chosen, in short, it was not fit for purpose. I expounded this on
the following wiki page:

-   [https://github.com/symfony-cmf/symfony-cmf/wiki/Query-Builder-v2](https://github.com/symfony-cmf/symfony-cmf/wiki/Query-Builder-v2)

As detailed in the above linked page, it seemed to me that either we
implemented a 2 part factory heavy query builder or a fluent node based
query builder. The node based design won. However, at the time I never
imagined it would take so long to write! So nearly 2 months later here
we are.

Some features of the new query builder

-   Can fully express the PHPCR QOM (Query Object Model).
-   Features a fluent interface.
-   Strict validation and helpful exception messages.
-   Less verbose than the PHPCR QOM model.

Lets compare a PHPCR QOM query with its new query builder counterpart:

The following two examples are equivalent and both select a blog posts
with node name "My Post Title" having the ODM class "BlogPost". We order
the result set first by publishing date and then by title.

Using the PHPCR QOM:

~~~~ {.sourceCode .php}
<?php
$q = $qom->createQuery(
    // SourceInterface (from)
    $qom->selector('nt:unstructured', 'p')
    $qom->andConstraint(
        $qom->comparison(
            $qom->propertyValue('p', 'phpcr:class'),
            $qom->bindVariableValue('phpcr_class'),
            QueryObjectModelInterface::JCR_OPERATOR_EQUAL_TO
        ),
        $qom->comparison(
            $qom->nodeLocalName('p'),
            $qom->bindVariableValue('post_title'),
            QueryObjectModelInterface::JCR_OPERATOR_EQUAL_TO
        ),
        array(
            $qom->ascending($qom->propertyValue('published_on', 'p'))
            $qom->ascending($qom->propertyValue('title', 'p'))
        )
    )
);
$q->bindValue('phpcr_class', 'Blog\Post');
$q->bindValue('post_title', 'My Post Title');
$res = $q->execute();
~~~~

Using the new PHPCR-ODM query builder:

~~~~ {.sourceCode .php}
<?php
$q = $documentManager->createQueryBuilder()
    ->fromDocument('Blog\Post', 'p')
    ->where()
        ->eq()->field('p.title')->parameter('post_title')->end()
    ->end()
    ->orderBy()
        ->asc()->field('p.published_on')->end()
        ->asc()->field('p.title')->end()
    ->end()
    ->setParameter('post_title', 'My Post Title')
    ->getQuery();
$res = $q->execute();
~~~~

Whilst the two examples above are equivalent it should be noted that we
are being slightly unfair to the PHPCR QOM as we are forced to add the
`phpcr:class` constraint, which is a PHPCR-ODMism. Despite this, the new
API is clearly less verbose and, I hope, more intelligible.

The API allows chaining together operands:

~~~~ {.sourceCode .php}
<?php
$qb = $documentManager->createQueryBuilder();
$qb
    ->from()
        ->document('Blog\Post', 'p')
    ->end()
    ->where()
        ->andX()
            ->orX()
                ->eq()->upperCase()->field('p.username')->end()->literal('DANTLEECH')->end()
                ->eq()->field('c.initials')->literal('dtl')->end()
            ->end()
            ->lte()->field('p.published_on')->literal('2013-09-14')->end()
        ->end()
    ->end();
~~~~

The API also allows you to break the query into multiple statements:

~~~~ {.sourceCode .php}
<?php
$qb->from()->document('Blog\Post', 'p');
$qb->where()->eq()->field('p.title')->literal('Foobar');
$qb->orderBy()->asc()->field('p.title');
~~~~

And to add extra criteria to an existing query builder instance (useful
if the query builder is instantiated and initialized by a vendor
library):

~~~~ {.sourceCode .php}
<?php
class MyExtension
{
    public function modifyQuery(QueryBuilder $qb)
    {
        $qb->andWhere()->field('f.site_id')->literal(1);
    }
}
~~~~

As a bonus, the nature of the API also allows us to easily add multiple
constraints to `andX` and `orX` operator nodes, something not easily
done with the native PHPCR builder:

~~~~ {.sourceCode .php}
<?php
$qb->fromDocument('Blog\Post', 'p');

// we can add one or many constraints to an "andX" node...
$qb->where()->andX()
    ->fieldIsset('p.username')
    ->gt()->field('p.rank')->literal(50)->end()
    ->eq()->fueld('p.title')->literal('This is a title');
~~~~

The documentation is now online and is made up of both a guide and a
reference:

-   Guide:
    [http://docs.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/reference/query-builder.html](http://docs.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/reference/query-builder.html)
-   Reference:
    [http://docs.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/reference/query-builder-reference.html](http://docs.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/reference/query-builder-reference.html)

