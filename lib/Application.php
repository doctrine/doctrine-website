<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\Website\Commands\BuildAllCommand;
use Doctrine\Website\Commands\BuildDatabaseCommand;
use Doctrine\Website\Commands\BuildDocsCommand;
use Doctrine\Website\Commands\BuildWebsiteCommand;
use Doctrine\Website\Commands\BuildWebsiteDataCommand;
use Doctrine\Website\Commands\ClearBuildCacheCommand;
use Doctrine\Website\Commands\SyncRepositoriesCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function assert;
use function date_default_timezone_set;
use function file_exists;
use function getenv;
use function is_string;
use function realpath;
use function sprintf;

final readonly class Application
{
    public const ENV_PROD    = 'prod';
    public const ENV_STAGING = 'staging';

    public function __construct(
        private BaseApplication $application,
        BuildAllCommand $buildAllCommand,
        BuildDocsCommand $buildDocsCommand,
        BuildWebsiteCommand $buildWebsiteCommand,
        BuildWebsiteDataCommand $buildWebsiteDataCommand,
        ClearBuildCacheCommand $clearBuildCacheCommand,
        SyncRepositoriesCommand $syncRepositoriesCommand,
        BuildDatabaseCommand $buildDatabaseCommand,
    ) {
        $this->application->add($buildAllCommand);
        $this->application->add($buildDocsCommand);
        $this->application->add($buildWebsiteCommand);
        $this->application->add($buildWebsiteDataCommand);
        $this->application->add($clearBuildCacheCommand);
        $this->application->add($syncRepositoriesCommand);
        $this->application->add($buildDatabaseCommand);

        $this->application->setHelperSet(new HelperSet([
            'question'      => new QuestionHelper(),
        ]));
    }

    public function run(InputInterface $input): int
    {
        $inputOption = new InputOption(
            'env',
            'e',
            InputOption::VALUE_REQUIRED,
            'The environment.',
            'dev',
        );
        $this->application->getDefinition()->addOption($inputOption);

        return $this->application->run($input);
    }

    public function getConsoleApplication(): BaseApplication
    {
        return $this->application;
    }

    public static function getContainer(string $env): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.website.env', $env);
        $container->setParameter('doctrine.website.debug', $env !== self::ENV_PROD);
        $container->setParameter('doctrine.website.root_dir', realpath(__DIR__ . '/..'));
        $container->setParameter('doctrine.website.config_dir', realpath(__DIR__ . '/../config'));
        $container->setParameter('doctrine.website.cache_dir', realpath(__DIR__ . '/../cache'));
        $container->setParameter('doctrine.website.github.http_token', getenv('doctrine_website_github_http_token'));
        $container->setParameter('doctrine.website.algolia.admin_api_key', getenv('doctrine_website_algolia_admin_api_key') ?: '1234');
        $container->setParameter('doctrine.website.stripe.secret_key', getenv('doctrine_website_stripe_secret_key') ?: '');
        $container->setParameter('doctrine.website.send_grid.api_key', getenv('doctrine_website_send_grid_api_key') ?: '');

        $xmlConfigLoader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $xmlConfigLoader->load('services.xml');

        $yamlConfigLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $yamlConfigLoader->load('routes.yml');

        $yamlConfigLoader->load(sprintf('config_%s.yml', $env));

        $configDir = $container->getParameter('doctrine.website.config_dir');
        assert(is_string($configDir));
        if (file_exists($configDir . '/local.yml')) {
            $yamlConfigLoader->load('local.yml');
        }

        $container->compile();

        date_default_timezone_set('America/New_York');

        return $container;
    }
}
