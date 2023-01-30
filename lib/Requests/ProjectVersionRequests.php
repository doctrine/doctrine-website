<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;

class ProjectVersionRequests
{
    /** @param ProjectRepository<Project> $projectRepository */
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function getProjectVersions(): RequestCollection
    {
        /** @var Project[] $projects */
        $projects = $this->projectRepository->findAll();

        $requests = [];

        foreach ($projects as $project) {
            foreach ($project->getVersions() as $version) {
                $requests[] = [
                    'slug' => $project->getSlug(),
                    'versionSlug' => $version->getSlug(),
                ];
            }
        }

        return new ArrayRequestCollection($requests);
    }
}
