<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\ContributorHydrator;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;

class ContributorHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(ContributorHydrator::class);
        $propertyValues = [
            'teamMember' => new TeamMember(),
            'github' => 'github',
            'avatarUrl' => 'avatarUrl',
            'numCommits' => 1,
            'numAdditions' => 2,
            'numDeletions' => 3,
            'projects' => [new Project()],
        ];

        $expected = new Contributor();
        $this->populate($expected, $propertyValues);

        $contributor = new Contributor();

        $hydrator->hydrate($contributor, $propertyValues);

        self::assertEquals($expected, $contributor);
    }
}
