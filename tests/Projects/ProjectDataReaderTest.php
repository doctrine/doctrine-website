<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectDataReader;
use Doctrine\Website\Tests\TestCase;
use InvalidArgumentException;

class ProjectDataReaderTest extends TestCase
{
    /** @var string[][] */
    private $projectIntegrationTypes;

    /** @var ProjectDataReader */
    private $projectDataReader;

    public function testRead(): void
    {
        self::assertSame([
            'name' => 'test-project',
            'repositoryName' => 'test-project',
            'docsPath' => '/docs',
            'codePath' => '/src',
            'slug' => 'test-project',
            'versions' => [
                [
                    'name' => 'master',
                    'branchName' => 'master',
                    'slug' => 'latest',
                    'aliases' => [
                        'current',
                        'stable',
                    ],
                ],
            ],
            'composerPackageName' => 'doctrine/test-project',
            'description' => 'Test description',
            'keywords' => ['keyword1', 'keyword2'],
            'shortName' => 'test',
            'docsSlug' => 'test-project',
        ], $this->projectDataReader->read('test-project'));
    }

    public function testReadFileDoesNotExist(): void
    {
        self::assertEquals([
            'repositoryName' => 'no-project-json',
            'name' => 'no-project-json',
            'docsPath' => null,
            'codePath' => '/',
            'slug' => 'no-project-json',
            'versions' => [
                [
                    'name' => 'master',
                    'branchName' => 'master',
                    'slug' => 'latest',
                    'aliases' => [
                        'current',
                        'stable',
                    ],
                ],
            ],
            'docsSlug' => 'no-project-json',
        ], $this->projectDataReader->read('no-project-json'));
    }

    public function testReadFileHasInvalidJson(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->projectDataReader->read('invalid-project-json');
    }

    public function testProjectIntegrationType(): void
    {
        $projectData = $this->projectDataReader->read('test-integration-project');

        self::assertSame($this->projectIntegrationTypes['symfony'], $projectData['integrationType']);
    }

    protected function setUp(): void
    {
        $this->projectIntegrationTypes = [
            'symfony' => [
                'name' => 'Symfony',
                'url' => 'https://symfony.com',
                'icon' => 'https://symfony.com/logos/symfony_black_03.png',
            ],
        ];

        $this->projectDataReader = new ProjectDataReader(
            __DIR__ . '/../test-projects',
            [
                [
                    'repositoryName' => 'test-integration-project',
                    'integration' => true,
                    'integrationType' => 'symfony',
                ],
            ],
            $this->projectIntegrationTypes
        );
    }
}
