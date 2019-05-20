<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Command as DBALCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration as MigrationsConfiguration;
use Doctrine\Migrations\Tools\Console\Command as MigrationsCommand;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\Website\Commands\BuildAllCommand;
use Doctrine\Website\Commands\BuildDocsCommand;
use Doctrine\Website\Commands\BuildWebsiteCommand;
use Doctrine\Website\Commands\BuildWebsiteDataCommand;
use Doctrine\Website\Commands\ClearBuildCacheCommand;
use Doctrine\Website\Commands\DeployCommand;
use Doctrine\Website\Commands\EventParticipantsCommand;
use Doctrine\Website\Commands\SyncRepositoriesCommand;
use Stripe;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function date_default_timezone_set;
use function file_exists;
use function getenv;
use function realpath;
use function sprintf;

class Application
{
    /** @var BaseApplication */
    private $application;

    public function __construct(
        BaseApplication $application,
        EntityManager $em,
        Connection $connection,
        MigrationsConfiguration $migrationsConfiguration,
        BuildAllCommand $buildAllCommand,
        BuildDocsCommand $buildDocsCommand,
        BuildWebsiteCommand $buildWebsiteCommand,
        BuildWebsiteDataCommand $buildWebsiteDataCommand,
        ClearBuildCacheCommand $clearBuildCacheCommand,
        DeployCommand $deployCommand,
        SyncRepositoriesCommand $syncRepositoriesCommand,
        EventParticipantsCommand $eventParticipantsCommand
    ) {
        $this->application = $application;

        $this->application->add($buildAllCommand);
        $this->application->add($buildDocsCommand);
        $this->application->add($buildWebsiteCommand);
        $this->application->add($buildWebsiteDataCommand);
        $this->application->add($clearBuildCacheCommand);
        $this->application->add($deployCommand);
        $this->application->add($syncRepositoriesCommand);
        $this->application->add($eventParticipantsCommand);

        $this->application->setHelperSet(new HelperSet([
            'question'      => new QuestionHelper(),
            'db'            => new ConnectionHelper($connection),
            'em'            => new EntityManagerHelper($em),
            'configuration' => new ConfigurationHelper($connection, $migrationsConfiguration),
        ]));

        $this->application->addCommands([
            // DBAL Commands
            new DBALCommand\ReservedWordsCommand(),
            new DBALCommand\RunSqlCommand(),

            // ORM Commands
            new ORMCommand\ClearCache\CollectionRegionCommand(),
            new ORMCommand\ClearCache\EntityRegionCommand(),
            new ORMCommand\ClearCache\MetadataCommand(),
            new ORMCommand\ClearCache\QueryCommand(),
            new ORMCommand\ClearCache\QueryRegionCommand(),
            new ORMCommand\ClearCache\ResultCommand(),
            new ORMCommand\SchemaTool\CreateCommand(),
            new ORMCommand\SchemaTool\UpdateCommand(),
            new ORMCommand\SchemaTool\DropCommand(),
            new ORMCommand\EnsureProductionSettingsCommand(),
            new ORMCommand\GenerateProxiesCommand(),
            new ORMCommand\RunDqlCommand(),
            new ORMCommand\ValidateSchemaCommand(),
            new ORMCommand\InfoCommand(),
            new ORMCommand\MappingDescribeCommand(),

            // Migrations Commands
            new MigrationsCommand\DumpSchemaCommand(),
            new MigrationsCommand\ExecuteCommand(),
            new MigrationsCommand\GenerateCommand(),
            new MigrationsCommand\LatestCommand(),
            new MigrationsCommand\MigrateCommand(),
            new MigrationsCommand\RollupCommand(),
            new MigrationsCommand\StatusCommand(),
            new MigrationsCommand\VersionCommand(),
            new MigrationsCommand\UpToDateCommand(),
            new MigrationsCommand\DiffCommand(),
        ]);
    }

    public function run(InputInterface $input) : int
    {
        $inputOption = new InputOption(
            'env',
            'e',
            InputOption::VALUE_REQUIRED,
            'The environment.',
            'dev'
        );
        $this->application->getDefinition()->addOption($inputOption);

        return $this->application->run($input);
    }

    public function getConsoleApplication() : BaseApplication
    {
        return $this->application;
    }

    public static function getContainer(string $env) : ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.website.env', $env);
        $container->setParameter('doctrine.website.debug', $env !== 'prod');
        $container->setParameter('doctrine.website.root_dir', realpath(__DIR__ . '/..'));
        $container->setParameter('doctrine.website.config_dir', realpath(__DIR__ . '/../config'));
        $container->setParameter('doctrine.website.cache_dir', realpath(__DIR__ . '/../cache'));
        $container->setParameter('doctrine.website.github.http_token', getenv('doctrine_website_github_http_token'));

        $xmlConfigLoader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $xmlConfigLoader->load('services.xml');

        $yamlConfigLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $yamlConfigLoader->load('routes.yml');

        $yamlConfigLoader->load(sprintf('config_%s.yml', $env));

        if (file_exists($container->getParameter('doctrine.website.config_dir') . '/local.yml')) {
            $yamlConfigLoader->load('local.yml');
        }

        $dataLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../data'));
        $dataLoader->load('events.yml');
        $dataLoader->load('partners.yml');
        $dataLoader->load('projects.yml');
        $dataLoader->load('sponsors.yml');
        $dataLoader->load('team_members.yml');

        $container->compile();

        Stripe\Stripe::setApiKey($container->getParameter('doctrine.website.stripe.secret_key'));

        date_default_timezone_set('America/New_York');

        return $container;
    }
}
