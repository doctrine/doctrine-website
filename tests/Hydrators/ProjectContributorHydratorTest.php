<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\ProjectContributorHydrator;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectContributor;
use Doctrine\Website\Model\TeamMember;

class ProjectContributorHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(ProjectContributorHydrator::class);
        $propertyValues = [
            'teamMember' => new TeamMember(),
            'projectSlug' => 'projectSlug',
            'github' => 'github',
            'avatarUrl' => 'avatarUrl',
            'numCommits' => 1,
            'numAdditions' => 2,
            'numDeletions' => 3,
            'project' => new Project(),
        ];

        $expected = new ProjectContributor();
        $this->populate($expected, $propertyValues);

        $doctrineUser = new ProjectContributor();

        $hydrator->hydrate($doctrineUser, $propertyValues);

        self::assertEquals($expected, $doctrineUser);
    }
}
