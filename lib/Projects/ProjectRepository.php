<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use function array_map;
use function ksort;
use function sprintf;

class ProjectRepository
{
    /** @var string[][]|int[][]|bool[][] */
    private $projects = [];

    /** @var ProjectFactory */
    private $projectFactory;

    /**
     * @param string[][]|int[][]|bool[][] $projects
     */
    public function __construct(array $projects, ProjectFactory $projectFactory)
    {
        $this->projects       = $projects;
        $this->projectFactory = $projectFactory;
    }

    public function findOneBySlug(string $slug) : Project
    {
        foreach ($this->projects as $project) {
            if ($project['slug'] === $slug || $project['docsSlug'] === $slug) {
                return $this->projectFactory->create($project);
            }
        }

        throw new InvalidArgumentException(sprintf('Could not find Project with slug "%s"', $slug));
    }

    /**
     * @return Project[]
     */
    public function findAll() : array
    {
        $projects = array_map(function (array $project) : Project {
            return $this->projectFactory->create($project);
        }, $this->projects);

        ksort($projects);

        return $projects;
    }
}
