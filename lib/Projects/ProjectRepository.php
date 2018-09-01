<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use function array_map;
use function sprintf;
use function usort;

class ProjectRepository
{
    /** @var string[] */
    private $projectRepositoryNames = [];

    /** @var ProjectFactory */
    private $projectFactory;

    /** @var Project[] */
    private $projects = [];

    /**
     * @param string[] $projectRepositoryNames
     */
    public function __construct(array $projectRepositoryNames, ProjectFactory $projectFactory)
    {
        $this->projectRepositoryNames = $projectRepositoryNames;
        $this->projectFactory         = $projectFactory;
    }

    /**
     * @return string[]
     */
    public function getProjectRepositoryNames() : array
    {
        return $this->projectRepositoryNames;
    }

    public function findOneBySlug(string $slug) : Project
    {
        $this->init();

        foreach ($this->projects as $project) {
            if ($project->getSlug() === $slug || $project->getDocsSlug() === $slug) {
                return $project;
            }
        }

        throw new InvalidArgumentException(sprintf('Could not find Project with slug "%s"', $slug));
    }

    /**
     * @return Project[]
     */
    public function findAll() : array
    {
        $this->init();

        return $this->projects;
    }

    private function init() : void
    {
        if ($this->projects !== []) {
            return;
        }

        $this->projects = array_map(function (string $repositoryName) : Project {
            return $this->projectFactory->create($repositoryName);
        }, $this->projectRepositoryNames);

        usort($this->projects, function (Project $a, Project $b) {
            return $a->getName() <=> $b->getName();
        });
    }
}
