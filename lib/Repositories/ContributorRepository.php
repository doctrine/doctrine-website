<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Contributor;
use UnexpectedValueException;

use function sprintf;

class ContributorRepository extends BasicObjectRepository
{
    public function findOneByGithub(string $github): Contributor
    {
        $contributor = $this->findOneBy(['github' => $github]);

        if (! $contributor instanceof Contributor) {
            throw new UnexpectedValueException(sprintf('No contributor was found by "%s"', $github));
        }

        return $contributor;
    }

    /**
     * @return Contributor[]
     */
    public function findMaintainers(): array
    {
        return $this->findBy(['isTeamMember' => true], ['github' => 'asc']);
    }

    /**
     * @return Contributor[]
     */
    public function findContributors(): array
    {
        return $this->findBy(['isTeamMember' => false], ['github' => 'asc']);
    }
}
