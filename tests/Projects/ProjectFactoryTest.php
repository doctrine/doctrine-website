<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectDataReader;
use Doctrine\Website\Projects\ProjectFactory;
use Doctrine\Website\Tests\TestCase;

class ProjectFactoryTest extends TestCase
{
    /** @var ProjectDataReader */
    private $projectDataReader;

    /** @var ProjectFactory */
    private $projectFactory;

    protected function setUp() : void
    {
        $this->projectDataReader = new ProjectDataReader(__DIR__ . '/../test-projects', []);

        $this->projectFactory = new ProjectFactory($this->projectDataReader);
    }

    public function testCreate() : void
    {
        $project = $this->projectFactory->create('no-project-json');

        self::assertSame('no-project-json', $project->getRepositoryName());
    }

    public function testCreateWithDoctrineProjectJson() : void
    {
        $project = $this->projectFactory->create('test-project');

        self::assertSame('test-project', $project->getRepositoryName());
        self::assertSame('test', $project->getShortName());
        self::assertSame('doctrine/test-project', $project->getComposerPackageName());
        self::assertSame('Test description', $project->getDescription());
        self::assertSame(['keyword1', 'keyword2'], $project->getKeywords());
    }
}
