<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;

final class GetTotalDownloads
{
    /** @param ProjectRepository<Project> $projectRepository */
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function __invoke(): int
    {
        $totalDownloads = 0;

        foreach ($this->projectRepository->findAll() as $project) {
            $totalDownloads += $project->getProjectStats()->getTotalDownloads();
        }

        return $totalDownloads;
    }
}
