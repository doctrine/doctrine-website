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
            [
                'name' => 'ocramius',
                'active' => true,
                'core' => true,
                'projects' => ['orm'],
            ],
            [
                'name' => 'jwage',
                'active' => true,
                'documentation' => true,
                'projects' => ['orm'],
            ],
            [
                'name' => 'romanb',
                'active' => false,
                'projects' => ['orm'],
            ],
        ]);
    }

    public function testGetTeamMembersData() : void
    {
        $teamMembers = $this->teamMembers->getData();

        self::assertSame([
            [
                'active' => true,
                'core' => false,
                'documentation' => true,
                'name' => 'jwage',
                'projects' => ['orm'],
            ],
            [
                'active' => true,
                'core' => true,
                'documentation' => false,
                'name' => 'ocramius',
                'projects' => ['orm'],
            ],
            [
                'active' => false,
                'core' => false,
                'documentation' => false,
                'name' => 'romanb',
                'projects' => ['orm'],
            ],
        ], $teamMembers);
    }
}
