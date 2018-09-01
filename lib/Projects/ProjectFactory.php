<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

class ProjectFactory
{
    /** @var ProjectJsonReader */
    private $projectJsonReader;

    public function __construct(ProjectJsonReader $projectJsonReader)
    {
        $this->projectJsonReader = $projectJsonReader;
    }

    public function create(string $repositoryName) : Project
    {
        return new Project($this->projectJsonReader->read($repositoryName));
    }
}
