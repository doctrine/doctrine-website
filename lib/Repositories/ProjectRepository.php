<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Project;
use InvalidArgumentException;
use function sprintf;

class ProjectRepository extends BasicObjectRepository
{
    /**
     * @return Project[]
     */
    public function findAll() : array
    {
        /** @var Project[] $projects */
        $projects = parent::findAll();

        return $projects;
    }

    public function findOneByDocsSlug(string $docsSlug) : Project
    {
        $project = $this->findOneBy(['docsSlug' => $docsSlug]);

        if ($project === null) {
            throw new InvalidArgumentException(sprintf('Could not find Project with docsSlug "%s"', $docsSlug));
        }

        return $project;
    }

    /**
     * @return Project[]
     */
    public function findPrimaryProjects() : array
    {
        return $this->findBy([
            'active' => true,
            'integration' => false,
        ], ['name' => 'asc']);
    }

    /**
     * @return Project[]
     */
    public function findInactiveProjects() : array
    {
        return $this->findBy([
            'active' => false,
            'archived' => false,
        ], ['name' => 'asc']);
    }

    /**
     * @return Project[]
     */
    public function findArchivedProjects() : array
    {
        return $this->findBy([
            'active' => false,
            'archived' => true,
        ], ['name' => 'asc']);
    }

    /**
     * @return Project[]
     */
    public function findIntegrationProjects() : array
    {
        return $this->findBy([
            'active' => true,
            'integration' => true,
        ], ['name' => 'asc']);
    }

    /**
     * @return Project[]
     */
    public function findProjectIntegrations(Project $project) : array
    {
        return $this->findBy([
            'integration' => true,
            'integrationFor' => $project->getSlug(),
        ], ['name' => 'asc']);
    }
}
