<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

return static function (ContainerConfigurator $container): void {
    $services   = $container->services();
    $parameters = $container->parameters();

    $services->set(Configuration::class)
        ->args([['lib/Model', 'lib/Git', 'lib/Docs/RST']])
        ->factory([ORMSetup::class, 'createAttributeMetadataConfiguration'])
        ->call('enableNativeLazyObjects', [true]);

    $services->set(Connection::class)
        ->args([['driver' => 'pdo_sqlite', 'path' => '%doctrine.website.cache_dir%/doctrine-website.sqlite']])
        ->factory([DriverManager::class, 'getConnection']);

    $services->set(EntityManager::class)
        ->args([
            service(Connection::class),
            service(Configuration::class),
        ]);

    $services->alias(EntityManagerInterface::class, EntityManager::class)
        ->public();
};
