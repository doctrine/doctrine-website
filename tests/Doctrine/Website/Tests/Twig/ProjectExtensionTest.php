<?php

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\Twig\ProjectExtension;
use PHPUnit\Framework\TestCase;

class ProjectExtensionTest extends TestCase
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ProjectExtension */
    private $projectExtension;

    protected function setUp()
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectExtension = new ProjectExtension($this->projectRepository);
    }

    public function testGetProjects()
    {
        $projects = [
            'orm' => [],
            'dbal' => [],
        ];

        $this->projectRepository->expects($this->once())
            ->method('findActive')
            ->willReturn($projects);

        $this->assertEquals($projects, $this->projectExtension->getProjects());
    }

    public function testGetProject()
    {
        $project = new Project([]);

        $this->projectRepository->expects($this->once())
            ->method('findOneBySlug')
            ->with('orm')
            ->willReturn($project);

        $this->assertSame($project, $this->projectExtension->getProject('orm'));
    }

    public function testGetUrlVersion()
    {
        $version = new ProjectVersion([
            'slug' => '2.0',
        ]);

        $this->assertEquals('/test/2.0', $this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0'));
    }
}
