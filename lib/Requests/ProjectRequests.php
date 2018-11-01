<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;

class ProjectRequests
{
    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getProjects() : RequestCollection
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
