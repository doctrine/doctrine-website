<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use function array_replace;

class ProjectFactory
{
    /** @var ProjectJsonReader */
    private $projectJsonReader;

    public function __construct(ProjectJsonReader $projectJsonReader)
    {
        $this->projectJsonReader = $projectJsonReader;
    }

    /**
     * @param mixed[] $project
     */
    public function create(array $project) : Project
    {
        if (! isset($project['repositoryName'])) {
            throw new InvalidArgumentException('You must configure a repositoryName for the project.');
        }

        $projectJson = $this->projectJsonReader->read($project['repositoryName']);

        $projectData = array_replace($projectJson, $project);

        return new Project($projectData);
    }
}
