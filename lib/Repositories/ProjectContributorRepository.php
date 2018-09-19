<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectContributor;

class ProjectContributorRepository extends BasicObjectRepository
{
    /**
     * @return ProjectContributor[]
     */
    public function findAll() : array
    {
        /** @var ProjectContributor[] $projectContributors */
        $projectContributors = $this->findBy([], ['github' => 'asc']);

        return $projectContributors;
    }

    /**
     * @return ProjectContributor[]
     */
    public function findMaintainersByProject(Project $project) : array
    {
        return $this->findBy([
            'isMaintainer' => true,
            'projectSlug' => $project->getSlug(),
        ], ['github' => 'asc']);
    }

    /**
     * @return ProjectContributor[]
     */
    public function findContributorsByProject(Project $project) : array
    {
        return $this->findBy([
            'isMaintainer' => false,
            'projectSlug' => $project->getSlug(),
        ], ['github' => 'asc']);
    }
}
