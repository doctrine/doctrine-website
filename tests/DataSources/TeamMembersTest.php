<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\TeamMembers;
use Doctrine\Website\Tests\TestCase;

class TeamMembersTest extends TestCase
{
    /** @var TeamMembers */
    private $teamMembers;

    protected function setUp() : void
    {
        $this->teamMembers = new TeamMembers([
            ['name' => 'ocramius'],
            ['name' => 'jwage'],
            ['name' => 'romanb'],
        ]);
    }

    public function testGetTeamMembersData() : void
    {
        $teamMembers = $this->teamMembers->getData();

        self::assertSame([
            ['name' => 'ocramius'],
            ['name' => 'jwage'],
            ['name' => 'romanb'],
        ], $teamMembers);
    }
}
