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
    private $projects = [];

    /** @var ProjectFactory */
    private $projectFactory;

    /** @var Project[] */
    private $projectObjects = [];

    /**
     * @param string[] $projects
     */
    public function __construct(array $projects, ProjectFactory $projectFactory)
    {
        $this->projects       = $projects;
        $this->projectFactory = $projectFactory;
    }

    public function findOneBySlug(string $slug) : Project
    {
        $this->init();

        foreach ($this->projectObjects as $project) {
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

        return $this->projectObjects;
    }

    private function init() : void
    {
        if ($this->projectObjects !== []) {
            return;
        }

        $this->projectObjects = array_map(function (string $repositoryName) : Project {
            return $this->projectFactory->create($repositoryName);
        }, $this->projects);

        usort($this->projectObjects, function (Project $a, Project $b) {
            return $a->getName() <=> $b->getName();
        });
    }
}
