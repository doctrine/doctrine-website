<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;

class TeamMemberRepository extends BasicObjectRepository
{
    /**
     * @return TeamMember[]
     */
    public function findAll() : array
    {
        /** @var TeamMember[] $teamMembers */
        $teamMembers = parent::findAll();

        return $teamMembers;
    }

    /**
     * @return TeamMember[]
     */
    public function getActiveCoreTeamMembers() : array
    {
        return $this->findBy([
            'active' => true,
            'core' => true,
        ]);
    }

    /**
     * @return TeamMember[]
     */
    public function getActiveDocumentationTeamMembers() : array
    {
        return $this->findBy([
            'active' => true,
            'documentation' => true,
        ]);
    }

    /**
     * @return TeamMember[]
     */
    public function getInactiveTeamMembers() : array
    {
        return $this->findBy(['active' => false]);
    }

    /**
     * @return TeamMember[]
     */
    public function getAllProjectTeamMembers(Project $project) : array
    {
        return $this->findBy([
            'projects' => ['$contains' => $project->getSlug()],
        ]);
    }

    /**
     * @return TeamMember[]
     */
    public function getActiveProjectTeamMembers(Project $project) : array
    {
        return $this->findBy([
            'active' => true,
            'projects' => ['$contains' => $project->getSlug()],
        ]);
    }

    /**
     * @return TeamMember[]
     */
    public function getInactiveProjectTeamMembers(Project $project) : array
    {
        return $this->findBy([
            'active' => false,
            'projects' => ['$contains' => $project->getSlug()],
        ]);
    }
}
