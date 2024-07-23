<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Website\Application;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectStats;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_merge;

abstract class TestCase extends BaseTestCase
{
    private static ContainerBuilder|null $container = null;

    protected function getContainer(): ContainerBuilder
    {
        if (self::$container === null) {
            self::$container = Application::getContainer('test');
        }

        return self::$container;
    }

    /** @param mixed[] $data */
    protected function createProject(array $data): Project
    {
        $default = [
            'projectStats' => new ProjectStats(),
            'active' => true,
            'archived' => false,
            'name' => '',
            'shortName' => '',
            'slug' => '',
            'docsSlug' => '',
            'composerPackageName' => '',
            'repositoryName' => '',
            'integrationFor' => '',
            'docsRepositoryName' => '',
            'docsPath' => '',
            'codePath' => '',
            'description' => '',
            'projectIntegrationType' => null,
            'integration' => true,
            'keywords' => [],
            'versions' => new ArrayCollection(),
        ];

        $data = array_merge($default, $data);

        return new Project(...$data);
    }
}
