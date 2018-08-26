<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Team;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Team\TeamRepository;
use PHPUnit\Framework\TestCase;
use function array_keys;

class TeamRepositoryTest extends TestCase
{
    /** @var TeamRepository */
    private $teamRepository;

    protected function setUp() : void
    {
        $this->teamRepository = new TeamRepository([
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
        $teamMembers = $this->teamRepository->getTeamMembers();

        self::assertSame([
            'jwage',
            'ocramius',
            'romanb',
        ], array_keys($teamMembers));
    }

    public function testGetActiveCoreTeamMembers() : void
    {
        $teamMembers = $this->teamRepository->getActiveCoreTeamMembers();

        self::assertSame(['ocramius'], array_keys($teamMembers));
    }

    public function testGetActiveDocumentationTeamMembers() : void
    {
        $teamMembers = $this->teamRepository->getActiveDocumentationTeamMembers();

        self::assertSame(['jwage'], array_keys($teamMembers));
    }

    public function testGetInactiveTeamMembers() : void
    {
        $teamMembers = $this->teamRepository->getInactiveTeamMembers();

        self::assertSame(['romanb'], array_keys($teamMembers));
    }

    public function testGetAllProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->teamRepository->getAllProjectTeamMembers($project);

        self::assertSame([
            'jwage',
            'ocramius',
            'romanb',
        ], array_keys($teamMembers));
    }

    public function testGetActiveProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->teamRepository->getActiveProjectTeamMembers($project);

        self::assertSame([
            'jwage',
            'ocramius',
        ], array_keys($teamMembers));
    }

    public function testGetInactiveProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->teamRepository->getInactiveProjectTeamMembers($project);

        self::assertSame(['romanb'], array_keys($teamMembers));
    }
}
