---
title: Experimental Doctrine 2 Feature: Associated Entities as Id Fields
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: []
permalink: /2010/08/07/dc2-experimental-associations-id-fields.html
---
Doctrine 2 supports composite keys of primitive types from the
beginning, however we realized early that a very common use-case is a
composite key with one or more elements being associated entities. For
example think of a CMS System which allows Article Translations. A
common SQL schema for this case would be:

    [sql]
    CREATE TABLE article (id INT PRIMARY KEY, title VARCHAR(255), body LONGTEXT);

    CREATE TABLE article_translations (article_id INT, language CHAR(3), title VARCHAR(255), body LONGTEXT, PRIMARY KEY (article_id, language));

This sort of schema cannot be mapped with Doctrine 2 currently, it would
be required to add another column `id` on the article\_translations
table and enforce a unique constraint on article\_id + language.

Under the umbrella of
[DDC-117](http://www.doctrine-project.org/jira/browse/DDC-117) and some
related tickets there were discussions about adding a feature that would
help solve this problem: Allowing to add @Id to @ManyToOne or @OneToOne
mappings. I committed this feature into an experimental Git branch today
and we ask you to test this with as many crazy scenarios and use-cases
as possible.

This feature can potentially be uber-powerful, however we want to be
sure that it works correctly and does not have to many problematic
edge-cases. Therefore we need your feedback.

-   Go to
    [[http://github.com/doctrine/doctrine2/commits/DDC-117](http://github.com/doctrine/doctrine2/commits/DDC-117)](http://github.com/doctrine/doctrine2/commits/DDC-117)
    to see the code
-   [Have a look at the functional
    tests](http://github.com/doctrine/doctrine2/blob/DDC-117/tests/Doctrine/Tests/ORM/Functional/Ticket/DDC117Test.php)
    to see the synatx
-   Checkout the Git Repository and switch to the experimental branch
    `git checkout DDC-117`
-   Do crazy testing against this branch!

Personally I want this feature in core very much, however composite keys
are very tricky. We want to find as many problematic cases with this
feature as possible. That would enable us to evaluate if this approach
will be merged into Doctrine 2.1 or not.

> **NOTE**
>
> This feature currently only works with Annotations Mapping Driver. XML
> and YAML will follow later.

By the way, the previous example is actually one of the functional
test-cases for this feature:

~~~~ {.sourceCode .php}
<?php
/**
 * @Entity
 */
class DDC117Article
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;
    /** @Column */
    private $title;

    /**
     * @OneToMany(targetEntity="DDC117Translation", mappedBy="article")
     */
    private $translations;

    public function __construct($title)
    {
        $this->title = $title;
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

/**
 * @Entity
 */
class DDC117Translation
{
    /**
     * @Id @ManyToOne(targetEntity="DDC117Article")
     */
    private $article;

    /**
     * @Id @column(type="string")
     */
    private $language;

    /**
     * @column(type="string")
     */
    private $title;

    public function __construct($article, $language, $title)
    {
        $this->article = $article;
        $this->language = $language;
        $this->title = $title;
    }
}

$article = new DDC117Article("foo");
$em->persist($article);
$em->flush();

$translation = new DDC117Translation($article, "en", "Bar");
$em->persist($translation);
$em->flush();
~~~~
