<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\ProjectHydrator;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectIntegrationType;
use Doctrine\Website\Model\ProjectStats;
use Doctrine\Website\Model\ProjectVersion;

class ProjectHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(ProjectHydrator::class);
        $propertyValues = [
            'active' => false,
            'archived' => true,
            'name' => 'name',
            'shortName' => 'shortName',
            'slug' => 'slug',
            'docsSlug' => 'docsSlug',
            'composerPackageName' => 'composerPackageName',
            'repositoryName' => 'repositoryName',
            'integration' => true,
            'integrationFor' => 'integrationFor',
            'docsRepositoryName' => 'docsRepositoryName',
            'docsPath' => 'docsPath',
            'codePath' => 'codePath',
            'description' => 'description',
            'keywords' => ['keywords'],
            'versions' => [
                ['name' => 'v1'],
                new ProjectVersion(['name' => 'v2']),
            ],
            'integrationType' => [],
            'packagistData' => [
                'package' => [
                    'github_stars' => 1,
                    'github_watchers' => 2,
                    'github_forks' => 3,
                    'github_open_issues' => 4,
                    'dependents' => 5,
                    'suggesters' => 6,
                    'downloads' => [
                        'total' => 7,
                        'monthly' => 8,
                        'daily' => 9,
                    ],
                ],
            ],
        ];

        $expected = new Project();
        $this->populate($expected, [
            'active' => false,
            'archived' => true,
            'name' => 'name',
            'shortName' => 'shortName',
            'slug' => 'slug',
            'docsSlug' => 'docsSlug',
            'composerPackageName' => 'composerPackageName',
            'repositoryName' => 'repositoryName',
            'isIntegration' => true,
            'integrationFor' => 'integrationFor',
            'docsRepositoryName' => 'docsRepositoryName',
            'docsPath' => 'docsPath',
            'codePath' => 'codePath',
            'description' => 'description',
            'keywords' => ['keywords'],
            'versions' => [
                new ProjectVersion(['name' => 'v1']),
                new ProjectVersion(['name' => 'v2']),
            ],
            'projectIntegrationType' => new ProjectIntegrationType([]),
            'projectStats' => new ProjectStats(1, 2, 3, 4, 5, 6, 7, 8, 9),
        ]);

        $project = new Project();

        $hydrator->hydrate($project, $propertyValues);

        self::assertEquals($expected, $project);
    }

    public function testHydrateDefaultValues(): void
    {
        $hydrator       = $this->createHydrator(ProjectHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'slug' => 'slug',
            'repositoryName' => 'repositoryName',
            'versions' => [],
        ];

        $expected = new Project();
        $this->populate($expected, [
            'active' => true,
            'archived' => false,
            'name' => 'name',
            'shortName' => 'name',
            'slug' => 'slug',
            'docsSlug' => 'slug',
            'composerPackageName' => '',
            'repositoryName' => 'repositoryName',
            'isIntegration' => false,
            'integrationFor' => '',
            'docsRepositoryName' => 'repositoryName',
            'docsPath' => '/docs',
            'codePath' => '/lib',
            'description' => '',
            'keywords' => [],
            'versions' => [],
            'projectIntegrationType' => null,
            'projectStats' => new ProjectStats(0, 0, 0, 0, 0, 0, 0, 0, 0),
        ]);

        $project = new Project();

        $hydrator->hydrate($project, $propertyValues);

        self::assertEquals($expected, $project);
    }

    public function testHydrateNoVersions(): void
    {
        $hydrator       = $this->createHydrator(ProjectHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'slug' => 'slug',
            'repositoryName' => 'repositoryName',
        ];

        $expected = new Project();
        $this->populate($expected, [
            'active' => true,
            'archived' => false,
            'name' => 'name',
            'shortName' => 'name',
            'slug' => 'slug',
            'docsSlug' => 'slug',
            'composerPackageName' => '',
            'repositoryName' => 'repositoryName',
            'isIntegration' => false,
            'integrationFor' => '',
            'docsRepositoryName' => 'repositoryName',
            'docsPath' => '/docs',
            'codePath' => '/lib',
            'description' => '',
        ]);

        $project = new Project();

        $hydrator->hydrate($project, $propertyValues);

        self::assertEquals($expected, $project);
    }

    public function testHydrateWithFilter(): void
    {
        $hydrator       = $this->createHydrator(ProjectHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'slug' => 'slug',
            'repositoryName' => 'repositoryName',
            'versionFilter' => '/^2\.0/',
            'versions' => [
                ['name' => '1.0.0'],
                new ProjectVersion(['name' => '2.0.0']),
            ],
        ];

        $expected = [
            new ProjectVersion(['name' => '2.0.0']),
        ];

        $project = new Project();

        $hydrator->hydrate($project, $propertyValues);

        self::assertEquals($expected, $project->getVersions());
    }
}
