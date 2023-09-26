<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

/** @final */
class ProjectDataRepository
{
    /** @param mixed[][] $projectsData */
    public function __construct(
        private readonly array $projectsData = [],
    ) {
    }

    /** @return string[] */
    public function getProjectRepositoryNames(): array
    {
        $projectRepositoryNames = [];

        foreach ($this->projectsData as $projectData) {
            $projectRepositoryNames[] = $projectData['repositoryName'];
        }

        return $projectRepositoryNames;
    }
}
