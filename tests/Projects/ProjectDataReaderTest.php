<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectDataReader;
use Doctrine\Website\Tests\TestCase;
use InvalidArgumentException;

class ProjectDataReaderTest extends TestCase
{
    /** @var ProjectDataReader */
    private $projectDataReader;

    public function testRead() : void
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
        ], $this->projectDataReader->read('test-project'));
    }

    public function testReadFileDoesNotExist() : void
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
            'keywords' => ['keyword1', 'keyword2'],
        ], $this->projectDataReader->read('no-project-json'));
    }

    public function testReadFileHasInvalidJson() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->projectDataReader->read('invalid-project-json');
    }

    protected function setUp() : void
    {
        $this->projectDataReader = new ProjectDataReader(__DIR__ . '/../test-projects', [
            [
                'repositoryName' => 'no-project-json',
                'keywords' => ['keyword1', 'keyword2'],
            ],
        ]);
    }
}
