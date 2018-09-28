<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\Website\Commands\BuildDocsCommand;
use Doctrine\Website\Commands\BuildWebsiteCommand;
use Doctrine\Website\Commands\ClearBuildCacheCommand;
use Doctrine\Website\Commands\DeployCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
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

    /** @var ClearBuildCacheCommand */
    private $clearBuildCacheCommand;

    /** @var DeployCommand */
    private $deployCommand;

    public function __construct(
        BaseApplication $application,
        BuildDocsCommand $buildDocsCommand,
        BuildWebsiteCommand $buildWebsiteCommand,
        ClearBuildCacheCommand $clearBuildCacheCommand,
        DeployCommand $deployCommand
    ) {
        $this->application            = $application;
        $this->buildDocsCommand       = $buildDocsCommand;
        $this->buildWebsiteCommand    = $buildWebsiteCommand;
        $this->clearBuildCacheCommand = $clearBuildCacheCommand;
        $this->deployCommand          = $deployCommand;
    }

    public function run(InputInterface $input) : int
    {
        $this->application->add($this->buildDocsCommand);
        $this->application->add($this->buildWebsiteCommand);
        $this->application->add($this->clearBuildCacheCommand);
        $this->application->add($this->deployCommand);

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

        $loader->load(sprintf('config_%s.yml', $env));

        if (file_exists($container->getParameter('doctrine.website.config_dir') . '/local.yml')) {
            $loader->load('local.yml');
        }

        $container->compile();

        return $container;
    }
}
