<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\GetTotalDownloads;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Tests\TestCase;

class GetTotalDownloadsTest extends TestCase
{
    public function testGetTotalDownloadsTest(): void
    {
        $project1 = $this->createProject($this->createProjectData(21));
        $project2 = $this->createProject($this->createProjectData(13));
        $project3 = $this->createProject($this->createProjectData(8));

        $projectRepository = self::createStub(ProjectRepository::class);
        $projectRepository->method('findAll')
            ->willReturn([$project1, $project2, $project3]);

        $getTotalDownloads = new GetTotalDownloads($projectRepository);

        self::assertSame(42, $getTotalDownloads());
    }

    /** @return array<string, mixed> */
    private function createProjectData(int $totalDownloads): array
    {
        return [
            'packagistData' => [
                'package' => [
                    'downloads' => ['total' => $totalDownloads],
                ],
            ],
            'versions' => [],
        ];
    }
}
