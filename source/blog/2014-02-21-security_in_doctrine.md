---
title: "Security in Doctrine"
authorName: default
authorEmail:
categories: []
permalink: /2014/02/21/security_in_doctrine.html
---
When using Doctrine in a project it is always a security critical
component because it talks directly to your database. As such security
is very important to us. In security however, context is important and
providing you with query capabilities we have to expose you to the risk
of SQL injections.

Doctrine cannot prevent you from building SQL injections into your
applications and so can no other DBAL, because it would require hiding
SQL completely. But hiding SQL completely is not wanted, because it is
such a powerful language.

Therefore it is still your responsibility to make sure that you are
using Doctrine correctly when working with SQL.

But how would you know how to do so? Until now we had some small bits
about security here and there in the documentation, mostly in the
chapters about Query objects. We came to the conclusion that this is not
enough.

That conclusion sinked in with a security issue we became aware of last
month, where one Doctrine user reported that one of the core
`Doctrine\DBAL\Connection` APIs is supposedly vulnerable to SQL
injection. When you use `$connection->insert($tableName, $values)`, then
both the table name and the keys (columns) of the `$values` array are
not escaped, because we assume they are never user input.

We evaluated this issue together with [Padraic
Brady](http://blog.astrumfutura.com/) (a known PHP security researcher)
and came to the conclusion that this is not a security issue for us.
Why? Because we don't think this part of the API can be secured and
trying will make our users feel safe, when they are not. Using the DBAL
APIs directly always posed a much higher risk than using just the ORM.

You might think we are nuts by just claiming a non issue, but consider
the assumptions we make about tables and columns and our reasoning:

-   Quoting identifiers is bad, because it changes them from
    case-insensitive to case-sensitive. Even more weird, Oracle unquoted
    identifiers are uppercased, PostgreSQL unquoted identifiers are
    lowercased. MySQL casing is based on a config option. Doctrine 1.\*
    had various unfixable bugs because of identifier quoting, which is
    why we decided that Doctrine will not use automatic identifier
    quoting.

-   The APIs of `Connection#insert()`, `Connection#update()` and
    `Connection#delete()` therefore accept both quoted and unquoted
    table/column identifiers, because quoting is the users choice.

-   A mechanism to detect SQL injection in strings that can be either
    quoted or unquoted is impossible to write completely secure. There
    are too many edge cases to consider and there is a realistic chance
    to miss one of them.

-   If you provide an API that is just secure in 99.999% of all cases,
    then you should not claim it is secure at all.

At this point you can still think we are wrong releasing insecure
software, however let me ask back: Isn't PHP shipping insecure software
by providing PDO? SQL injection is possible by using PDO wrong. I can
enumerate lots of libraries where security is the developers
responsibility: Template engines, authentication libraries and so on.

A proper secured system requires knowledge about the context. That is
why any kind of database abstraction layers can never fully protect you
from SQL injection, because it does not know the context you are using
it in.

To avoid secret knowledge about our security assumptions we are now
starting to be completely explicit about these issues. Both DBAL and ORM
now contain a \`SECURITY.md\` file, which contains basic information
about security and links to much more detailed documentation chapters on
Doctrine security.

We have made an effort to list all the functions and operations that are
safe from SQL injection. There are not very many of them in the DBAL,
because it is such a low level library. The ORM however is pretty
secure, except when concatenating user input into DQL and SQL queries.

Read all the information about Security in Doctrine in the
documentation.

-   [DBAL
    Security](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/security.html)
-   [ORM
    Security](http://docs.doctrine-project.org/en/latest/reference/security.html)

