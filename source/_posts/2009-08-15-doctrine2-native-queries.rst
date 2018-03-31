---
title: Doctrine 2 Native Queries
menuSlug: blog
authorName: romanb 
authorEmail: 
categories: []
indexed: false
---
If you are familar with Doctrine\_RawSql from Doctrine 1.x you
probably know that it is somewhat broken by design since it
requires a special syntax in the select clause that makes a lot of
SQL constructs in the select clause impossible.

Doctrine 2 introduces a facility called "native queries",
represented by the ``Doctrine\ORM\NativeQuery`` class that replaces
Doctrine\_RawSql. The clue about NativeQuery is that it allows the
usage of real native SQL yet the result can be transformed into all
the different formats that are supported by DQL.

This is achieved through having a generic description of how an SQL
result maps to a Doctrine result in form of a
``Doctrine\ORM\Query\ResultSetMapping``.

Enough of the introductory talk, lets look at a (primitive) example
from our test suite:

.. code-block:: php

    <?php
    $rsm = new ResultSetMapping;
    $rsm->addEntityResult('Doctrine\Tests\Models\CMS\CmsUser', 'u');
    $rsm->addFieldResult('u', 'id', 'id'); // ($alias, $columnName, $fieldName)
    $rsm->addFieldResult('u', 'name', 'name'); // // ($alias, $columnName, $fieldName)
    
    $query = $this->_em->createNativeQuery('SELECT id, name FROM cms_users WHERE username = ?', $rsm);
    $query->setParameter(1, 'romanb');
    
    $users = $query->getResult();
    
    $this->assertEquals(1, count($users));
    $this->assertTrue($users[0] instanceof CmsUser);
    $this->assertEquals('Roman', $users[0]->getName());

The SQL that is passed to createNativeQuery is not touched by
Doctrine in any way. The NativeQuery and ResultSetMapping
combination are extremely powerful. It might not surprise you that
Doctrine 2 creates a ResultSetMapping internally when it transforms
DQL to SQL.

Granted, this was a very trivial example that can easily be
expressed in a short DQL 1-liner but it was just for demonstration
purposes (and I am bad at making up complex ad-hoc SQL examples
that make sense).

So when is this useful? While DQL has been heavily improved in
Doctrine 2 and has many new powerful features which we will cover
in future blog posts, it is sometimes necessary to use native SQL.
NativeQuery gives you the ability to do that while still retrieving
the results in the convenient Doctrine formats you are familar
with. NativeQuery is also an obvious choice when starting to
migrate a pure SQL project to Doctrine without going into all the
DQL details from the start.

More on NativeQuery and ResultSetMapping can be found in the new
documentation under:

`Documentation - Native Sql <http://www.doctrine-project.org/documentation/manual/2_0/en/native-sql>`_
