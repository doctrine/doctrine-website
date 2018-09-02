<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use function array_replace;

class ProjectFactory
{
    /** @var ProjectJsonReader */
    private $projectJsonReader;

    /** @var mixed[] */
    private $projectsConfiguration;

    /**
     * @param mixed[] $projectsConfiguration
     */
    public function __construct(ProjectJsonReader $projectJsonReader, array $projectsConfiguration)
    {
        $this->projectJsonReader     = $projectJsonReader;
        $this->projectsConfiguration = $projectsConfiguration;
    }

    public function create(string $repositoryName) : Project
    {
        $project = $this->projectJsonReader->read($repositoryName);

        if (isset($this->projectsConfiguration[$repositoryName])) {
            $project = array_replace(
                $this->projectsConfiguration[$repositoryName],
                $project
            );
        }

        return new Project($project);
    }
}
