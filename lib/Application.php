<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\Website\Commands\BuildDocsCommand;
use Doctrine\Website\Commands\BuildWebsiteCommand;
use Doctrine\Website\Commands\BuildWebsiteDataCommand;
use Doctrine\Website\Commands\ClearBuildCacheCommand;
use Doctrine\Website\Commands\DeployCommand;
use Doctrine\Website\Commands\SyncRepositoriesCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function file_exists;
use function getenv;
use function realpath;
use function sprintf;

class Application
{
    /** @var BaseApplication */
    private $application;

    /** @var BuildDocsCommand */
    private $buildDocsCommand;

    /** @var BuildWebsiteCommand */
    private $buildWebsiteCommand;

    /** @var BuildWebsiteDataCommand */
    private $buildWebsiteDataCommand;

    /** @var ClearBuildCacheCommand */
    private $clearBuildCacheCommand;

    /** @var DeployCommand */
    private $deployCommand;

    /** @var SyncRepositoriesCommand */
    private $syncRepositoriesCommand;

    public function __construct(
        BaseApplication $application,
        BuildDocsCommand $buildDocsCommand,
        BuildWebsiteCommand $buildWebsiteCommand,
        BuildWebsiteDataCommand $buildWebsiteDataCommand,
        ClearBuildCacheCommand $clearBuildCacheCommand,
        DeployCommand $deployCommand,
        SyncRepositoriesCommand $syncRepositoriesCommand
    ) {
        $this->application             = $application;
        $this->buildDocsCommand        = $buildDocsCommand;
        $this->buildWebsiteCommand     = $buildWebsiteCommand;
        $this->buildWebsiteDataCommand = $buildWebsiteDataCommand;
        $this->clearBuildCacheCommand  = $clearBuildCacheCommand;
        $this->deployCommand           = $deployCommand;
        $this->syncRepositoriesCommand = $syncRepositoriesCommand;
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

        $this->application->add($this->buildDocsCommand);
        $this->application->add($this->buildWebsiteCommand);
        $this->application->add($this->buildWebsiteDataCommand);
        $this->application->add($this->clearBuildCacheCommand);
        $this->application->add($this->deployCommand);
        $this->application->add($this->syncRepositoriesCommand);

        return $this->application->run($input);
    }

    public static function getContainer(string $env) : ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.website.env', $env);
        $container->setParameter('doctrine.website.root_dir', realpath(__DIR__ . '/..'));
        $container->setParameter('doctrine.website.config_dir', realpath(__DIR__ . '/../config'));
        $container->setParameter('doctrine.website.cache_dir', realpath(__DIR__ . '/../cache'));
        $container->setParameter('doctrine.website.github.http_token', getenv('doctrine_website_github_http_token'));

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.xml');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('projects.yml');
        $loader->load('team_members.yml');
        $loader->load('routes.yml');

        $loader->load(sprintf('config_%s.yml', $env));

        if (file_exists($container->getParameter('doctrine.website.config_dir') . '/local.yml')) {
            $loader->load('local.yml');
        }

        $container->compile();

        return $container;
    }
}
