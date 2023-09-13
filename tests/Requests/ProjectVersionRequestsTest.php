<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Requests\ProjectVersionRequests;
use Doctrine\Website\Tests\TestCase;

class ProjectVersionRequestsTest extends TestCase
{
    public function testGetProjectVersions(): void
    {
        $partner           = $this->createModel(ProjectRepository::class, [
            'slug' => 'project',
            'versions' => [
                ['slug' => 'v1'],
                ['slug' => 'v2'],
            ],
        ]);
        $projectRepository = $this->createMock(ProjectRepository::class);
        $projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([$partner]);

        $partnerRequest  = new ProjectVersionRequests($projectRepository);
        $projectVersions = $partnerRequest->getProjectVersions();

        $expects = new ArrayRequestCollection([
            [
                'slug' => 'project',
                'versionSlug' => 'v1',
            ],
            [
                'slug' => 'project',
                'versionSlug' => 'v2',
            ],
        ]);

        self::assertEquals($expects, $projectVersions);
    }
}
