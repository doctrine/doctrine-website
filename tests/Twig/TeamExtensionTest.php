<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Twig\TeamExtension;
use PHPUnit\Framework\TestCase;
use function array_keys;

class TeamExtensionTest extends TestCase
{
    /** @var TeamExtension */
    private $teamExtension;

    protected function setUp() : void
    {
        $this->teamExtension = new TeamExtension([
            'ocramius' => [
                'active' => true,
                'core' => true,
                'projects' => ['orm'],
            ],
            'jwage' => [
                'active' => true,
                'documentation' => true,
                'projects' => ['orm'],
            ],
            'romanb' => [
                'active' => false,
                'projects' => ['orm'],
            ],
        ]);
    }

    public function testGetTeamMembers() : void
    {
        $teamMembers = $this->teamExtension->getTeamMembers();

        self::assertSame([
            'jwage',
            'ocramius',
            'romanb',
        ], array_keys($teamMembers));
    }

    public function testGetActiveCoreTeamMembers() : void
    {
        $teamMembers = $this->teamExtension->getActiveCoreTeamMembers();

        self::assertSame(['ocramius'], array_keys($teamMembers));
    }

    public function testGetActiveDocumentationTeamMembers() : void
    {
        $teamMembers = $this->teamExtension->getActiveDocumentationTeamMembers();

        self::assertSame(['jwage'], array_keys($teamMembers));
    }

    public function testGetInactiveTeamMembers() : void
    {
        $teamMembers = $this->teamExtension->getInactiveTeamMembers();

        self::assertSame(['romanb'], array_keys($teamMembers));
    }

    public function testGetAllProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->teamExtension->getAllProjectTeamMembers($project);

        self::assertSame([
            'jwage',
            'ocramius',
            'romanb',
        ], array_keys($teamMembers));
    }

    public function testGetActiveProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->teamExtension->getActiveProjectTeamMembers($project);

        self::assertSame([
            'jwage',
            'ocramius',
        ], array_keys($teamMembers));
    }

    public function testGetInactiveProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->teamExtension->getInactiveProjectTeamMembers($project);

        self::assertSame(['romanb'], array_keys($teamMembers));
    }
}
