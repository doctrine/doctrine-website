<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Requests;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Requests\ProjectRequests;
use Doctrine\Website\StaticGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectRequestsTest extends TestCase
{
    /** @var ProjectRepository<Project>&MockObject */
    private ProjectRepository&MockObject $projectRepository;

    private ProjectRequests $projectRequests;

    public function testGetProjects(): void
    {
        $project1 = $this->createProject(['slug' => 'project1']);
        $project2 = $this->createProject(['slug' => 'project2']);

        $projects = [$project1, $project2];

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projects);

        $projectRequests = $this->projectRequests->getProjects();

        self::assertEquals(new ArrayRequestCollection([
            ['slug' => 'project1'],
            ['slug' => 'project2'],
        ]), $projectRequests);
    }

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);

        $this->projectRequests = new ProjectRequests(
            $this->projectRepository,
        );
    }
}
