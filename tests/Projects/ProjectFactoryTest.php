<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectFactory;
use Doctrine\Website\Projects\ProjectJsonReader;
use PHPUnit\Framework\TestCase;

class ProjectFactoryTest extends TestCase
{
    /** @var ProjectJsonReader */
    private $projectJsonReader;

    /** @var ProjectFactory */
    private $projectFactory;

    protected function setUp() : void
    {
        $this->projectJsonReader = new ProjectJsonReader(__DIR__ . '/../test-projects');

        $this->projectFactory = new ProjectFactory($this->projectJsonReader);
    }

    public function testCreate() : void
    {
        $project = $this->projectFactory->create([
            'slug' => 'test',
            'repositoryName' => 'test-project',
        ]);

        self::assertSame('test', $project->getSlug());
    }

    public function testCreateWithDoctrineProjectJson() : void
    {
        $project = $this->projectFactory->create([
            'slug' => 'test-project',
            'repositoryName' => 'test-project',
        ]);

        self::assertSame('test-project', $project->getSlug());
        self::assertSame('test', $project->getShortName());
        self::assertSame('doctrine/test-project', $project->getComposerPackageName());
        self::assertSame('Test description', $project->getDescription());
        self::assertSame(['keyword1', 'keyword2'], $project->getKeywords());
    }
}
