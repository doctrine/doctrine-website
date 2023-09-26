<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;

final readonly class ProjectRequests
{
    /** @param ProjectRepository<Project> $projectRepository */
    public function __construct(
        private ProjectRepository $projectRepository,
    ) {
    }

    public function getProjects(): RequestCollection
    {
        /** @var Project[] $projects */
        $projects = $this->projectRepository->findAll();

        $requests = [];

        foreach ($projects as $project) {
            $requests[] = [
                'slug' => $project->getSlug(),
            ];
        }

        return new ArrayRequestCollection($requests);
    }
}
