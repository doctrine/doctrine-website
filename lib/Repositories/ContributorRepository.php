<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Contributor;

class ContributorRepository extends BasicObjectRepository
{
    /**
     * @return Contributor[]
     */
    public function findMaintainers() : array
    {
        return $this->findBy(['isTeamMember' => true], ['github' => 'asc']);
    }

    /**
     * @return Contributor[]
     */
    public function findContributors() : array
    {
        return $this->findBy(['isTeamMember' => false], ['github' => 'asc']);
    }
}
