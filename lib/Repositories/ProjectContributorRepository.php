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
        $projectContributors = $this->findBy([], ['numCommits' => 'desc']);

        return $projectContributors;
    }

    /**
     * @return ProjectContributor[]
     */
    public function findCoreProjectContributorsByProject(Project $project) : array
    {
        return $this->findBy([
            'isTeamMember' => true,
            'projectSlug' => $project->getSlug(),
        ], ['numCommits' => 'desc']);
    }

    /**
     * @return ProjectContributor[]
     */
    public function findProjectContributorsByProject(Project $project) : array
    {
        return $this->findBy([
            'isTeamMember' => false,
            'projectSlug' => $project->getSlug(),
        ], ['numCommits' => 'desc']);
    }
}
