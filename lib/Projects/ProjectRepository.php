<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use function array_filter;
use function array_map;
use function sprintf;
use function usort;

class ProjectRepository
{
    /** @var mixed[][] */
    private $projectsData = [];

    /** @var ProjectFactory */
    private $projectFactory;

    /** @var Project[] */
    private $projects = [];

    /**
     * @param mixed[][] $projectsData
     */
    public function __construct(array $projectsData, ProjectFactory $projectFactory)
    {
        $this->projectsData   = $projectsData;
        $this->projectFactory = $projectFactory;
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

    /**
     * @return Project[]
     */
    public function findPrimaryProjects() : array
    {
        return array_filter($this->findAll(), static function (Project $project) : bool {
            return $project->isActive() && ! $project->isIntegration();
        });
    }

    /**
     * @return Project[]
     */
    public function findInactiveProjects() : array
    {
        return array_filter($this->findAll(), static function (Project $project) : bool {
            return ! $project->isActive() && ! $project->isArchived();
        });
    }

    /**
     * @return Project[]
     */
    public function findArchivedProjects() : array
    {
        return array_filter($this->findAll(), static function (Project $project) : bool {
            return ! $project->isActive() && $project->isArchived();
        });
    }

    /**
     * @return Project[]
     */
    public function findIntegrationProjects() : array
    {
        return array_filter($this->findAll(), static function (Project $project) : bool {
            return $project->isActive() && $project->isIntegration();
        });
    }

    /**
     * @return Project[]
     */
    public function findProjectIntegrations(Project $project) : array
    {
        return array_filter($this->findAll(), static function (Project $p) use ($project) : bool {
            return $p->isIntegration() && $p->getIntegrationFor() === $project->getSlug();
        });
    }

    private function init() : void
    {
        if ($this->projects !== []) {
            return;
        }

        $this->projects = array_map(function (array $projectData) : Project {
            return $this->projectFactory->create($projectData['repositoryName']);
        }, $this->projectsData);

        usort($this->projects, static function (Project $a, Project $b) {
            return $a->getName() <=> $b->getName();
        });
    }
}
