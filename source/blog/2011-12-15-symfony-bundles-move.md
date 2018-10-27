---
title: "Our Symfony Bundles move to Doctrine organization"
authorName: Benjamin Eberlei
authorEmail:
categories: []
permalink: /2011/12/15/symfony-bundles-move.html
---
The Symfony2 Doctrine related Bundles move to the Doctrine organization.
The DoctrineBundle being in the core for the 2.0 release it is now
maintained in a more decoupled way from Symfony for several reasons:

-   No coupling of release cycles anymore.
-   Move code to the organization that actually maintains it.
-   Avoid Symfony suggesting Doctrine is the only way for persistence,
    Symfony wants to focus on providing View and Controller and not make
    suggestions about the model.

The DoctrineFixturesBundle, DoctrineMigrationsBundle and
DoctrineMongoDBBundle are now maintained in the Doctrine organization,
however forks have been created in the Symfony repository to make all
the 2.0 apps out there backwards compatible. You find the new
repositories here:

-   [https://github.com/doctrine/DoctrineBundle](https://github.com/doctrine/DoctrineBundle)
-   [https://github.com/doctrine/DoctrineMongoDBBundle](https://github.com/doctrine/DoctrineMongoDBBundle)
-   [https://github.com/doctrine/DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle)
-   [https://github.com/doctrine/DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle)

What do you need to change in your code? Not very much. For the
DoctrineBundle for example the following:

-   Update the deps file to include the DoctrineBundle

    >     [empty]
    >     [DoctrineBundle]
    >         git=http://github.com/doctrine/DoctrineBundle.git
    >         target=/bundles/Doctrine/Bundle/DoctrineBundle

-   Change the Bundle class

-   Change references to Registry

The \`SymfonyBundleDoctrineBundleRegistry\` class may be type-hinted in
your code. You have to change this code to point to
\`DoctrineBundleDoctrineBundleRegistry\`.

A full deps file for all Doctrine bundles now looks like:

    [empty]
    [data-fixtures]
        git=http://github.com/doctrine/data-fixtures.git

    [migrations]
        git=http://github.com/doctrine/migrations.git

    [DoctrineBundle]
        git=http://github.com/doctrine/DoctrineBundle.git
        target=/bundles/Doctrine/Bundle/DoctrineBundle

    [DoctrineMigrationsBundle]
        git=http://github.com/doctrine/DoctrineMigrationsBundle.git
        target=/bundles/Doctrine/Bundle/MigrationsBundle

    [DoctrineFixturesBundle]
        git=http://github.com/doctrine/DoctrineFixturesBundle.git
        target=/bundles/Doctrine/Bundle/FixturesBundle

And the autoload.php:

~~~~ {.sourceCode .php}
$loader->registerNamespaces(array(
    'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
    'Sensio'           => __DIR__.'/../vendor/bundles',
    'JMS'              => __DIR__.'/../vendor/bundles',
    'Doctrine\\Bundle' => __DIR__.'/../vendor/bundles',
    'Doctrine\\DBAL\\Migrations' => __DIR__.'/../vendor/migrations/lib',
    'Doctrine\\Common\\DataFixtures' => __DIR__.'/../vendor/data-fixtures/lib',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'   => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine'         => __DIR__.'/../vendor/doctrine/lib',
    'Monolog'          => __DIR__.'/../vendor/monolog/src',
    'Assetic'          => __DIR__.'/../vendor/assetic/src',
    'Metadata'         => __DIR__.'/../vendor/metadata/src',
));
~~~~

And the Kernel:

~~~~ {.sourceCode .php}
$bundles = array(
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new Symfony\Bundle\MonologBundle\MonologBundle(),
    new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
    new Symfony\Bundle\AsseticBundle\AsseticBundle(),
    new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
    new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
    new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
);
~~~~
