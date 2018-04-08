<?php

namespace Doctrine\Website\Projects;

class ProjectRepository
{
    /** @var array */
    private $projects = [];

    /** @var ProjectFactory */
    private $projectFactory;

    public function __construct(array $projects, ProjectFactory $projectFactory)
    {
        $this->projects = $projects;
        $this->projectFactory = $projectFactory;
    }

    public function findOneBySlug(string $slug) : Project
    {
        foreach ($this->projects as $project) {
            if ($project['slug'] === $slug || $project['docsSlug'] === $slug) {
                return $this->projectFactory->create($project);
            }
        }
    }

    public function findAllActive() : array
    {
        return array_filter(array_map(function(array $project) {
            return $this->projectFactory->create($project);
        }, $this->projects), function(Project $project) {
            return $project->isActive();
        });
    }
}
