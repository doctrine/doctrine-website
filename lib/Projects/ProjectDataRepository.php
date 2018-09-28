<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

class ProjectDataRepository
{
    /** @var mixed[][] */
    private $projectsData = [];

    /**
     * @param mixed[][] $projectsData
     */
    public function __construct(array $projectsData)
    {
        $this->projectsData = $projectsData;
    }

    /**
     * @return string[]
     */
    public function getProjectRepositoryNames() : array
    {
        $projectRepositoryNames = [];

        foreach ($this->projectsData as $projectData) {
            $projectRepositoryNames[] = $projectData['repositoryName'];
        }

        return $projectRepositoryNames;
    }
}
