---
title: Cookbook Recipe: Relation DQL Behavior
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
Today I will teach you how to write a simple Doctrine behavior. You will
learn some of the basics of creating a behavior and you will gain some
pretty cool functionality for your relationships.

We will write a behavior called `RelationDql` which allows you to add
default query parts that are automatically added to your queries when
you reference the specified relationships. So first we will get started
by looking at an example schema we can apply this to.

Here is an example schema where we have a `Site`, `BlogPost` and `Tag`
model:

    [yml]
    Site:
      columns:
        name: string(255)

    BlogPost:
      actAs: [Timestampable]
      columns:
        title: string(255)
        body: clob
        site_id: integer
      relations:
        Site:
          foreignAlias: BlogPosts
        Tags:
          class: Tag
          refClass: BlogPostTag
          foreignAlias: BlogPosts

    Tag:
      columns:
        name: string(255)

    BlogPostTag:
      columns:
        blog_post_id:
          type: integer
          primary: true
        tag_id:
          type: integer
          primary: true
      relations:
        BlogPost:
          foreignAlias: BlogPostTags
        Tag:
          foreignAlias: BlogPostTags

This is a fairly simple schema as you can see, but what if we want to
have a relationship on the `Site` model to retrieve the latest five
`BlogPost` records or order the `Tags` relationship alphabetically by
default?

Lets modify our schema to take into account a new behavior that we will
write in the next step. First modify the `Site` model and a relationship
named `LatestBlogPosts`:

    [yml]
    Site:
      actAs:
        RelationDql:
          relations:
            LatestBlogPosts:
              orderBy: %s.created_at DESC
              limit: 5
      columns:
        name: string(255)
      relations:
        LatestBlogPosts:
          autoComplete: false
          class: BlogPost
          local: id
          foreign: site_id

    **TIP** The ``autoComplete`` option is set to ``false`` so that the
    relationship is not reflected and added to the opposite end,
    ``BlogPost`` automatically.

Now lets modify the `BlogPost` model to change the `Tags` relationship
so that it is ordered alphabetically by name by default:

    [yml]
    BlogPost:
      actAs:
        Timestampable:
        RelationDql:
          relations:
            Tags:
              orderBy: %s.name ASC
      columns:
        title: string(255)
        body: clob
        site_id: integer
      relations:
        Site:
          foreignAlias: BlogPosts
        Tags:
          class: Tag
          refClass: BlogPostTag
          foreignAlias: BlogPosts

    **TIP** The ``relations`` array is an array of changes to make to
    the DQL query. The key can be any valid function on the
    ``Doctrine_Query`` API and the value is of course the parameter to
    pass to the function.

Now that we have our schemas modified to take into account the new
`RelationDql` behavior we need to actually write the code:

~~~~ {.sourceCode .php}
<?php
class RelationDql extends Doctrine_Template
{
  protected $_options = array();

  public function __construct($options)
  {
    $this->_options = $options;
  }

  public function setTableDefinition()
  {
    $this->_table->addRecordListener(new RelationDqlListener($this->_options));
  }
}
~~~~

The template is very simple. It only attaches a record listener to the
invoking table. Their is where most of the magic happens. So now lets
define the `RelationDqlListener` class:

~~~~ {.sourceCode .php}
<?php
class RelationDqlListener extends Doctrine_Record_Listener
{
  protected $_options = array('relations' => array());

  public function __construct($options)
  {
    $this->_options = $options;
  }

  public function preDqlSelect(Doctrine_Event $event)
  {
    $query = $event->getQuery();

    if (empty($this->_options['relations']))
    {
      throw new Doctrine_Exception(
        'You must specify at least one relationship to add DQL to'
      );
    }

    $relations = $this->_options['relations'];

    $components = $this->_getDqlCallbackComponents($query);
    foreach ($components as $alias => $component)
    {
      if (isset($component['relation']) && isset($relations[$component['relation']->getAlias()]))
      {
        $dqls = $relations[$component['relation']->getAlias()];
        foreach ($dqls as $func => $dql)
        {
          $dql = str_replace('%s', $alias, $dql);
          $query->$func($dql);
        }
        unset($relations[$component['relation']->getAlias()]);
      }
    }
  }

  protected function _getDqlCallbackComponents($query)
  {
      $params = $query->getParams();
      $componentsBefore = array();
      if ($query->isSubquery()) {
          $componentsBefore = $query->getQueryComponents();
      }

      $copy = $query->copy();
      $copy->getSqlQuery($params);
      $componentsAfter = $copy->getQueryComponents();

      if ($componentsBefore !== $componentsAfter) {
          return array_diff($componentsAfter, $componentsBefore);
      } else {
          return $componentsAfter;
      }
  }
}
~~~~

So now we have the behavior defined so lets look at some example DQL
queries and the SQL that is outputted:

> **TIP** Remember, in order for the dql callbacks to be executed we
> must enable an attribute first.

~~~~ {.sourceCode .php}
<?php
    $manager->setAttribute('use_dql_callbacks', true);
~~~~

~~~~ {.sourceCode .php}
<?php
$q = Doctrine_Query::create()
  ->select('s.name, p.title, p.created_at')
  ->from('Site s')
  ->leftJoin('s.LatestBlogPosts p');

echo $q->getSql();
~~~~

The above would output the following SQL:

    [sql]
    SELECT s.id AS s__id, s.name AS s__name, b.id AS b__id, b.title AS b__title, b.created_at AS b__created_at FROM site s LEFT JOIN blog_post b ON s.id = b.site_id ORDER BY b.created_at DESC LIMIT 5

    **NOTE** Notice how the ``ORDER BY`` and ``LIMIT`` were added to
    the query.

Now lets look at an example that involves the `BlogPost` tags:

~~~~ {.sourceCode .php}
<?php
$q = Doctrine_Query::create()
  ->from('BlogPost p')
  ->leftJoin('p.Tags t');

echo $q->getSql();
~~~~

The above would output the following SQL query:

    [sql]
    SELECT b.id AS b__id, b.title AS b__title, b.body AS b__body, b.site_id AS b__site_id, b.created_at AS b__created_at, b.updated_at AS b__updated_at, t.id AS t__id, t.name AS t__name FROM blog_post b LEFT JOIN blog_post_tag b2 ON b.id = b2.blog_post_id LEFT JOIN tag t ON t.id = b2.tag_id ORDER BY t.name ASC

As you can see the `ORDER BY` clause to order the related tags by `name`
was added for us.

Pretty cool huh? You can use this in your projects to make your
relationships a little nicer.
