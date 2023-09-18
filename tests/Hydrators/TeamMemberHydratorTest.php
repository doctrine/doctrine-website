<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\TeamMemberHydrator;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Model\TeamMember;

class TeamMemberHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(TeamMemberHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'github' => 'github',
            'twitter' => 'twitter',
            'avatarUrl' => 'avatarUrl',
            'website' => 'website',
            'location' => 'location',
            'maintains' => ['orm'],
            'consultant' => true,
            'headshot' => 'headshot',
            'bio' => 'bio',
            'contributor' => static fn (string $github): Contributor => new Contributor(),
        ];

        $expected = new TeamMember();
        $this->populate($expected, $propertyValues);

        $teamMember = new TeamMember();

        $hydrator->hydrate($teamMember, $propertyValues);

        self::assertEquals($expected, $teamMember);
    }

    public function testHydrateContributorByClosure(): void
    {
        $hydrator = $this->createHydrator(TeamMemberHydrator::class);

        $teamMember = new TeamMember();

        $hydrator->hydrate($teamMember, []);

        self::assertEquals(new Contributor(), $teamMember->getContributor());
    }
}
