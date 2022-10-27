---
title: "From annotations to attributes"
authorName: Grégoire Paris
authorEmail: postmaster@greg0ire.fr
permalink: /2022/11/04/annotations-to-attributes.md
---

Last month, we migrated the tests of the ORM from annotations to attributes.
Let us look back on what lead to this moment.

## Annotations

Let's go 20 years back in time. In 2002, Alex Buckley, a Specification lead for
the Java language publishes [JSR-175][jsr-175], proposed to add user-defined
annotations to the language, allowing to tag language elements with extra
information. 2 years later, it gets approved and Java 1.5, also known as Java 5
is released, [with support for annotations][annotation-guide].

2 more years elapse and in 2004, Jano Suchal publishes [Addendum][addendum], a
PHP library that adds support for "Docblock/JavaDoc" annotations, meaning that
contrary to what is done in Java, Addendum annotations are contained inside
phpdoc comments, like this:

```php
/** @test */
function test_it_throws_on_invalid_argument(): void
{}
```

That is because they are implemented in userland, without requiring a change in
PHP itself.

Doctrine ORM 2.0 is not released yet at that point, but [the library is used to
build an annotation driver][addendum-common] in Doctrine 2 in early 2009.
At that time, Doctrine was a project in a single repository, with `Common`,
`DBAL` and `ORM` as top-level namespaces.
[Addendum is replaced 6 months later][annotation-driver], with a new namespace
under `Common` called `Annotations`.

In the summer of 2010, Guilherme Blanco and Pierrick Charron submit
[an RFC to add annotations support to PHP][annotations-rfc], but it
[gets declined][mailing-list-annotations-rfc]. The RFC already mentions the
need for annotations in PHPUnit, Symfony, Zend Framework, FLOW3 and of course,
Doctrine.

Late 2010, Doctrine 2 is tagged, and the single repository is split into 3
repositories.

Finally, in 2013, the namespace above is isolated in its own repository, and
`doctrine/annotations` 1.0.0 is tagged.

Today, the package is widely used in the PHP ecosystem and has a little short
of 300 million downloads on Packagist, and is depended on by over 2 thousand
packages, including major frameworks and tools. It is fair to say annotations
have proven valuable to many users.

[jsr-175]: https://www.cs.ubc.ca/~gregor/teaching/cpsc411/metadata-public-draft.html
[annotation-guide]: https://docs.oracle.com/javase/1.5.0/docs/guide/language/annotations.html
[addendum]: https://github.com/jsuchal/addendum
[addendum-common]: https://github.com/doctrine/orm/commit/bcf0110249978c8fc705916d5a4f94216bb98b07
[annotation-driver]: https://github.com/doctrine/orm/commit/9075f10bf5da3058f6ef82ff08e4783ff70424a4
[annotations-rfc]: https://wiki.php.net/rfc/annotations
[mailing-list-annotations-rfc]: https://externals.io/message/49733

## Attributes

The RFC mentioned above is only one among [many][rfc-list]. As mentioned
before, annotations were implemented as phpdoc comments, which has several
drawbacks:

- The comments are necessary to run the code, and [need to be kept in the
  opcode cache][opcache-save-comments].
- They are obtained at runtime, by using the reflection API, and because of
  that, can only be detected as invalid at runtime.
- They are not well supported by IDEs if at all.
- They clutter comments, which were originally intended for humans.
- They can be confused with phpdoc, which are something else entirely.

In March 2020, Benjamin Eberlei resurrects [Dmitry Stogov's attributes
RFC][attributes-rfc] and submits [the seventh RFC on this
topic][attributes-v2-rfc], introducing attributes to PHP.

A few rounds of RFCs about syntax later, PHP 8.0 is released, with a notable
feature missing: nested attributes. PHP 8.0 attributes use a syntax that is
forward-compatible with them though, and finally, with PHP 8.1, nested
attributes are supported.

[rfc-list]: https://wiki.php.net/rfc
[opcache-save-comments]: https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.save-comments
[attributes-rfc]: https://wiki.php.net/rfc/attributes
[attributes-v2-rfc]: https://wiki.php.net/rfc/attributes_v2

## Migrating from one to the other

Since attributes are much better than annotations, with `doctrine/orm` 3.0,
annotations will no longer be supported, which means applications using them as
a way to map entities to tables need to migrate towards attributes (or another
driver).
As maintainers of that library, even we needed to migrate: most of the test
suite of `doctrine/orm` used annotations.

Enter [Rector][rector]. Rector is a standalone tool that is invaluable when it
comes to performing such migrations: it is able to understand PHP code and
apply so-called Rectors to it. It is extensible, so it is possible to define
such Rectors in order to address upgrades for anything, including Doctrine.

What's more, it comes with batteries included: when you install
`rector/rector`, what you get is code from `rector/rector-src` _and_ its official
extensions, among which you will find [`rector/rector-doctrine`][rector-doctrine].
That's right, there is already an entire extension dedicated to Doctrine.

Rules are grouped together in sets, and the set that interests us here is
`Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES`.

To migrate `doctrine/orm`'s test suite to annotations, here is how we
proceeded:

1. Install Rector: `composer require --dev rector/rector`.
2. Create a file called `rector.php` at the root of the library with the
   following contents:

    ```php
        <?php

        declare(strict_types=1);

        use Rector\Config\RectorConfig;
        use Rector\Doctrine\Set\DoctrineSetList;

        return function (RectorConfig $rectorConfig): void {
            $rectorConfig->paths([
                __DIR__ . '/tests',
            ]);
            $rectorConfig->sets([
                DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
            ]);
        };
    ```
3. Run `vendor/bin/rector`, which obeys the above configuration.
4. Uninstall Rector: `composer remove rector/rector && rm rector.php`
5. Run `vendor/bin/phpcbf` to make the migrated codebase compliant with our
   coding standard.

Or at least, it was the plan, because some annotations were not perfectly
migrated. All in all, I found only [2][bug-1] [bugs][bug-2], which looks great
given how so many edge cases should appear in our test suite.

I went on and reported those 2 bugs, and this is where the experience went from
great to stellar:
the issue template leads to [a playground][demo], much like the one you can
find for other tools such as Psalm or PHPStan.

This one comes with 2 buttons: "Create an issue", which pre-fills the Github
issue with useful information, and "Create a test", that lets you create a test
in the right directory (and also, the right repository, which is
`rectorphp/rector-src`, and not `rectorphp/rector`).

If you want to add a new test for the bug you reported, you should let
[the official tutorial][rector-test-tutorial] walk you through that, it is very
well written.

Anyway, now that these 2 bugs are fixed and you know how to migrate, plan that
migration, and let us know how it goes! Happy Rectoring!

[rector]: https://github.com/rectorphp/rector
[rector-doctrine]: https://github.com/rectorphp/rector-doctrine
[bug-1]: https://github.com/rectorphp/rector/issues/7528
[bug-2]: https://github.com/rectorphp/rector-src/pull/2988
[demo]: https://getrector.org/demo
[rector-test-tutorial]: https://github.com/rectorphp/rector/blob/main/docs/how_to_add_test_for_rector_rule.md
