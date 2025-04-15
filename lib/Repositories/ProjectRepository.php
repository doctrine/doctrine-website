<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\Website\Model\Project;
use InvalidArgumentException;

use function sprintf;

/**
 * @template T of Project
 * @template-extends EntityRepository<T>
 */
class ProjectRepository extends EntityRepository
{
    public function findOneBySlug(string $slug): Project
    {
        $project = $this->findOneBy(['slug' => $slug]);

        if ($project === null) {
            throw new InvalidArgumentException(sprintf('Could not find Project with slug "%s"', $slug));
        }

        return $project;
    }

    public function findOneByDocsSlug(string $docsSlug): Project
    {
        $project = $this->findOneBy(['docsSlug' => $docsSlug]);

        if ($project === null) {
            throw new InvalidArgumentException(sprintf('Could not find Project with docsSlug "%s"', $docsSlug));
        }

        return $project;
    }

    /** @return Project[] */
    public function findPrimaryProjects(): array
    {
        return $this->findBy([
            'active' => true,
            'integration' => false,
        ], ['sortOrder' => 'asc', 'name' => 'asc']);
    }

    /** @return Project[] */
    public function findInactiveProjects(): array
    {
        return $this->findBy([
            'active' => false,
            'archived' => false,
        ], ['name' => 'asc']);
    }

    /** @return Project[] */
    public function findArchivedProjects(): array
    {
        return $this->findBy([
            'active' => false,
            'archived' => true,
        ], ['name' => 'asc']);
    }

    /** @return Project[] */
    public function findIntegrationProjects(): array
    {
        return $this->findBy([
            'active' => true,
            'integration' => true,
        ], ['sortOrder' => 'asc', 'name' => 'asc']);
    }

    /** @return Project[] */
    public function findProjectIntegrations(Project $project): array
    {
        return $this->findBy([
            'integration' => true,
            'integrationFor' => $project->getSlug(),
        ], ['sortOrder' => 'asc', 'name' => 'asc']);
    }
}
