<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Requests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Requests\ProjectVersionRequests;
use Doctrine\Website\StaticGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\Tests\TestCase;

class ProjectVersionRequestsTest extends TestCase
{
    public function testGetProjectVersions(): void
    {
        $partner           = $this->createProject([
            'slug' => 'project',
            'versions' => new ArrayCollection([
                new ProjectVersion(['slug' => 'v1']),
                new ProjectVersion(['slug' => 'v2']),
            ]),
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
