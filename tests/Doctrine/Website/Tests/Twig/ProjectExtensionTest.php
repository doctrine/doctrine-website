<?php

declare(strict_types=1);

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

    protected function setUp() : void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);

        $this->projectExtension = $this->getMockBuilder(ProjectExtension::class)
            ->setConstructorArgs([
                $this->projectRepository,
                '',
            ])
            ->setMethods(['fileExists'])
            ->getMock()
        ;
    }

    public function testGetProjects() : void
    {
        $projects = [
            'orm' => [],
            'dbal' => [],
        ];

        $this->projectRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($projects);

        $this->assertEquals($projects, $this->projectExtension->getProjects());
    }

    public function testGetProject() : void
    {
        $project = new Project([]);

        $this->projectRepository->expects($this->once())
            ->method('findOneBySlug')
            ->with('orm')
            ->willReturn($project);

        $this->assertSame($project, $this->projectExtension->getProject('orm'));
    }

    public function testGetUrlVersion() : void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        $this->projectExtension->expects($this->once())
            ->method('fileExists')
            ->with('/test/2.0')
            ->willReturn(true);

        $this->assertEquals(
            '/test/2.0',
            $this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0')
        );
    }

    public function testGetUrlVersionCurrent() : void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        $this->projectExtension->expects($this->once())
            ->method('fileExists')
            ->with('/test/2.0')
            ->willReturn(true);

        $this->assertEquals(
            '/test/2.0',
            $this->projectExtension->getUrlVersion($version, '/test/current', '1.0')
        );
    }

    public function testGetUrlVersionNotFound() : void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        $this->projectExtension->expects($this->once())
            ->method('fileExists')
            ->with('/test/2.0')
            ->willReturn(false);

        $this->assertNull($this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0'));
    }
}
