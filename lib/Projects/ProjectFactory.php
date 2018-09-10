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
        return new Project($this->projectDataReader->read($repositoryName));
    }
}
