---
title: "New Release: Doctrine MongoDB ODM 2.3 with Attributes, JSON Schema Validation, and more"
authorName: Ion Bazan
authorEmail: ion.bazan@gmail.com
permalink: /2021/12/01/mongodb-odm-2.3.html
---

We have released a new minor version 2.3 of Doctrine MongoDB ODM, the first version
with support for using PHP 8 Attributes as a new driver for mapping documents
and several other changes. [See all changes and contributors in the
Changelog](https://github.com/doctrine/mongodb-odm/releases/tag/2.3.0) on GitHub.

## Attributes Mapping Driver

The following code example shows many of the mappings that are re-using
the annotation classes for familiarity:

```php
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;

#[MongoDB\Document(repositoryClass: PostRepository::class)]
class Post
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\Field(type: Type::BOOLEAN)]
    private bool $published = false;

    #[MongoDB\Field(type: Types::COLLECTION)]
    private array $text = [];

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    public $author;

    #[MongoDB\ReferenceMany(targetDocument: Tag::class)]
    public Collection $tags;
}
```

You may want to use [Rector](https://getrector.org/) with `DoctrineSetList::DOCTRINE_ODM_23` set
to convert all your annotation mappings to attributes in seconds!

## JSON Schema Validation

MongoDB ≥ 3.6 offers the capability to validate documents during
insertions and updates through a JSON schema associated with the collection. 
[See MongoDB documentation](https://docs.mongodb.com/manual/core/schema-validation/).

Doctrine MongoDB ODM now provides a way to take advantage of this functionality
thanks to the new `#[Validation]` mapping.

```php
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;

#[MongoDB\Document]
#[MongoDB\Validation(
    validator: SchemaValidated::VALIDATOR,
    action: ClassMetadata::SCHEMA_VALIDATION_ACTION_WARN
)]
class SchemaValidated
{
    public const VALIDATOR = <<<'EOT'
{
    "$jsonSchema": {
        "required": ["name"],
        "properties": {
            "name": {
                "bsonType": "string",
                "description": "must be a string and is required"
            }
        }
    },
    "$or": [
        { "phone": { "$type": "string" } },
        { "email": { "$regex": { "$regularExpression" : { "pattern": "@mongodb\\.com$", "options": "" } } } },
        { "status": { "$in": [ "Unknown", "Incomplete" ] } }
    ]
}
EOT;
}


```

Once defined, those options will be added to the collection after running
the ``odm:schema:create`` or ``odm:schema:update`` command.

## Psalmified APIs

In-code documentation has been immensely improved to make sure static analysis tools and IDEs know
about the right document classes returned from `DocumentManager`,
`ClassMetadata`, and other public APIs. This includes generics support
for your own repositories extending `DocumentRepository`.

```php
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use App\Document\User;

/**
 * @template-extends DocumentRepository<User>
 */
class UserRepository extends DocumentRepository
{
}
```


## Deprecations

Doctrine MongoDB ODM 2.3 introduces several minor deprecations:

- The `Doctrine\ODM\MongoDB\Proxy\Resolver\ClassNameResolver` interface has been deprecated in favor 
  of the `Doctrine\Persistence\Mapping\ProxyClassNameResolver` interface
- Annotation classes no longer extend `Doctrine\Common\Annotations\Annotation` class
- Annotation arguments switched to `@NamedArgumentConstructor` for Attribute compatibility
- `@Inheritance` annotation has been removed as it was never used
- Document Namespace Aliases (`'MyBundle:User`) - use fully qualified class names instead (`User::class`)

## Coding Standard Support

Doctrine MongoDB ODM 2.3 now supports and fully validates against Doctrine Coding
Standard version 9.0+. This greatly improves automatic pull request checks as
all new violations in a PR get caught and inlined into the PR as comments.
