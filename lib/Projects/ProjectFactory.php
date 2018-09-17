<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

class ProjectFactory
{
    /** @var ProjectDataReader */
    private $projectDataReader;

    public function __construct(ProjectDataReader $projectDataReader)
    {
        $this->projectDataReader = $projectDataReader;
    }

    public function create(string $repositoryName) : Project
    {
        $projectData = $this->projectDataReader->read($repositoryName);

        if (isset($projectData['isIntegration']) && $projectData['isIntegration'] === true) {
            return new ProjectIntegration($projectData);
        }

        return new Project($projectData);
    }
}
