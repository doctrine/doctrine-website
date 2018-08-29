---
title: "Help write tests for new DQL Parser"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: guilhermeblanco
authorEmail:
categories: []
permalink: /2009/08/04/help-write-tests-for-new-dql-parser.html
---
As you all know, we're focusing most of our development time on Doctrine
2.0 these days. To help speed things up, we would like to ask for help
writing tests from our users

Recently I finished new [DQL parser for
2.0](http://trac.doctrine-project.org/browser/trunk/lib/Doctrine/ORM/Query/Parser.php).
Most of you may not be familiar with compiler's theory, but for those
that are, it's a top-down recursive descent parser for a context-free
grammar, usually known as LL(k).

We mapped the entire supported DQL into a document, which is an [EBNF
(Extended Backus-Naur
Form)](http://en.wikipedia.org/wiki/Extended_Backus–Naur_Form) , which
is a meta-syntax notation to express context-free grammars. This one is
quite simple to be readable by humans. Yes, we are humans if you raise
the question! =)

The header of our EBNF describes some terms and conventions, but here is
a small subset of our grammar:

    FromClause                        ::= "FROM" IdentificationVariableDeclaration {"," IdentificationVariableDeclaration}*
    IdentificationVariableDeclaration ::= RangeVariableDeclaration [IndexBy] {JoinVariableDeclaration}*
    RangeVariableDeclaration          ::= AbstractSchemaName ["AS"] AliasIdentificationVariable
    AbstractSchemaName                ::= identifier
    AliasIdentificationVariable       ::= identifier

It's not mentioned in this subset some pieces just for clarity.
Identifier is a terminal (no grammar rule to follow after that) and is a
string (ie. "`name`", "`email`").

What does this subset do?
=========================

It processes this pieces of DQL:

    FROM User u
    FROM User AS u, u.Group g

How does it do that?
====================

Each piece of DQL is converted to a series of tokens. Some tokens are
defined in our [Symbol Table](http://en.wikipedia.org/wiki/Symbol_table)
, which is then validated and correctly typed into the right token type.
For example... when it finds the "`FROM`", it'll return for us
internally a token in an array format of:

    array(
        'value' => 'FROM',
        'type' => Lexer::T_FROM,
        'position' => 0
    )

Value is the actual string representation in DQL, type is the token type
that was brought by symbol table and position is the position of this
token in the DQL string.

Then token is validated in the DQL parser; if it is expected the
`T_FROM`, just match and go to next token, if not raise a syntax error.
Imagine someone types this: "`FROM User u, u.Group u`". It is not valid
DQL, because alias "`u`" is being used twice. That is the task of
another check, which inspects the requested DQL defined symbol table and
reports for semantical errors. We removed the lexical errors since we
use them as identifiers, so there is no lexical error checks in DQL. Not
that it may open a hole in our structure, syntax and semantical checks
do all that we need.

How can I help?
===============

We currently have a single test that checks for DQL recognition. We need
to expand it to cover as much situations as possible. If you follow the
EBNF grammar rules, you'll see that for example, IN condition supports
either a series of Literals (which can be strings, booleans, etc), an
InputParameter or a subselect. We need some people with free time to
cover all possible situations; I mean that we needs something like these
queries:

    SELECT u FROM CmsUser u WHERE u.id IN (1, 2)

    SELECT u FROM CmsUser u WHERE u.id IN (?0)

    SELECT u FROM CmsUser u WHERE u.name IN ('guilhermeblanco', 'jwage', 'romanb')

    SELECT u FROM CmsUser u WHERE u.id IN (SELECT u2.id FROM CmsUser u2)

By doing (sometimes) stupid queries and complex queries, we cover all
possibilities and then we can finally consider that we have a good
coverage in our DQL support.

If you load the
[LanguageRecognitionTest](http://trac.doctrine-project.org/browser/trunk/tests/Doctrine/Tests/ORM/Query/LanguageRecognitionTest.php)
, you'll find that we have already builtin 2 methods to help you
asserting DQL support: `assertValidDql` and `assertInvalidDql`. Just
follow the EBNF, pick defined models under [Models/CMS
folder](http://trac.doctrine-project.org/browser/trunk/tests/Doctrine/Tests/Models/CMS)
and start writing tests.

Running tests
=============

It is not hard to execute new test suite. Once you have
[PHPUnit](http://phpunit.de) and [XDebug](http://xdebug.org) installed,
go to tests folder of trunk, create the directory `_coverage` (CHMOD
0777) and execute:

    phpunit --coverage-html=./_coverage Doctrine_Tests_AllTests

Then you'll have coverage too, which means it'll be even better to see
where it's missing tests in suite.

Once you write your tests, create a ticket in our trac and then upload
the patched file there. We'll review your tests and commit them.

To generate a patch file, just type:
`svn diff > /path/to/path/file.diff` I hope you enjoy new structure and
contribute with lots of tests!
