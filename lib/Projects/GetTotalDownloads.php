<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Repositories\ProjectRepository;

final class GetTotalDownloads
{
    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke() : int
    {
        $totalDownloads = 0;

        foreach ($this->projectRepository->findAll() as $project) {
            $totalDownloads += $project->getProjectStats()->getTotalDownloads();
        }

        return $totalDownloads;
    }
}
