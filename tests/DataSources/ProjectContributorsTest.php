<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\Website\DataSources\ProjectContributors;
use Doctrine\Website\Github\GithubProjectContributors;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProjectContributorsTest extends TestCase
{
    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var TeamMemberRepository|MockObject */
    private $teamMemberRepository;

    /** @var GithubProjectContributors|MockObject */
    private $githubProjectContributors;

    /** @var ProjectContributors */
    private $projectContributors;

    protected function setUp() : void
    {
        $this->projectRepository         = $this->createMock(ProjectRepository::class);
        $this->teamMemberRepository      = $this->createMock(TeamMemberRepository::class);
        $this->githubProjectContributors = $this->createMock(GithubProjectContributors::class);

        $this->projectContributors = new ProjectContributors(
            $this->projectRepository,
            $this->teamMemberRepository,
            $this->githubProjectContributors
        );
    }

    public function testGetSourceRows() : void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);

        $project1 = new Project(['slug' => 'orm']);
        $project2 = new Project(['slug' => 'dbal']);

        $jwageTeamMember = $this->createMock(TeamMember::class);

        $jwageTeamMember->expects(self::at(0))
            ->method('isProjectMaintainer')
            ->willReturn(true);

        $jwageTeamMember->expects(self::at(1))
            ->method('isProjectMaintainer')
            ->willReturn(false);

        $ocramiusTeamMember = $this->createMock(TeamMember::class);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([$project1, $project2]);

        $this->githubProjectContributors->expects(self::at(0))
            ->method('getProjectContributors')
            ->with($project1)
            ->willReturn([
                [
                    'weeks' => [
                        ['a' => 1, 'd' => 1],
                        ['a' => 2, 'd' => 3],
                    ],
                    'author' => [
                        'login' => 'jwage',
                        'avatar_url' => 'https://avatars1.githubusercontent.com/u/97422?s=60&v=4',
                    ],
                    'total' => 5,
                ],
                [
                    'weeks' => [
                        ['a' => 2, 'd' => 2],
                        ['a' => 2, 'd' => 3],
                    ],
                    'author' => [
                        'login' => 'ocramius',
                        'avatar_url' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                    ],
                    'total' => 10,
                ],
                [
                    'weeks' => [
                        ['a' => 2, 'd' => 2],
                        ['a' => 2, 'd' => 3],
                    ],
                    'author' => [
                        'login' => 'bob',
                        'avatar_url' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                    ],
                    'total' => 10,
                ],
            ]);

        $this->githubProjectContributors->expects(self::at(1))
            ->method('getProjectContributors')
            ->with($project2)
            ->willReturn([
                [
                    'weeks' => [
                        ['a' => 1, 'd' => 1],
                        ['a' => 2, 'd' => 3],
                    ],
                    'author' => [
                        'login' => 'jwage',
                        'avatar_url' => 'https://avatars1.githubusercontent.com/u/97422?s=60&v=4',
                    ],
                    'total' => 5,
                ],
                [
                    'weeks' => [
                        ['a' => 2, 'd' => 2],
                        ['a' => 2, 'd' => 3],
                    ],
                    'author' => [
                        'login' => 'ocramius',
                        'avatar_url' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                    ],
                    'total' => 10,
                ],
                [
                    'weeks' => [
                        ['a' => 2, 'd' => 2],
                        ['a' => 2, 'd' => 3],
                    ],
                    'author' => [
                        'login' => 'jim',
                        'avatar_url' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                    ],
                    'total' => 10,
                ],
            ]);

        $this->teamMemberRepository->expects(self::at(0))
            ->method('findOneByGithub')
            ->with('jwage')
            ->willReturn($jwageTeamMember);

        $this->teamMemberRepository->expects(self::at(1))
            ->method('findOneByGithub')
            ->with('ocramius')
            ->willReturn($ocramiusTeamMember);

        $this->teamMemberRepository->expects(self::at(2))
            ->method('findOneByGithub')
            ->with('bob')
            ->willReturn(null);

        $this->teamMemberRepository->expects(self::at(3))
            ->method('findOneByGithub')
            ->with('jwage')
            ->willReturn($jwageTeamMember);

        $this->teamMemberRepository->expects(self::at(4))
            ->method('findOneByGithub')
            ->with('ocramius')
            ->willReturn($ocramiusTeamMember);

        $this->teamMemberRepository->expects(self::at(5))
            ->method('findOneByGithub')
            ->with('jim')
            ->willReturn(null);

        $rows = $this->projectContributors->getSourceRows();

        self::assertEquals([
            [
                'teamMember' => $jwageTeamMember,
                'isTeamMember' => true,
                'isMaintainer' => true,
                'projectSlug' => 'orm',
                'project' => $project1,
                'github' => 'jwage',
                'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=60&v=4',
                'numCommits' => 5,
                'numAdditions' => 3,
                'numDeletions' => 4,
            ],
            [
                'teamMember' => $ocramiusTeamMember,
                'isTeamMember' => true,
                'isMaintainer' => false,
                'projectSlug' => 'orm',
                'project' => $project1,
                'github' => 'ocramius',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
            [
                'teamMember' => null,
                'isTeamMember' => false,
                'isMaintainer' => false,
                'projectSlug' => 'orm',
                'project' => $project1,
                'github' => 'bob',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
            [
                'teamMember' => $jwageTeamMember,
                'isTeamMember' => true,
                'isMaintainer' => false,
                'projectSlug' => 'dbal',
                'project' => $project2,
                'github' => 'jwage',
                'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=60&v=4',
                'numCommits' => 5,
                'numAdditions' => 3,
                'numDeletions' => 4,
            ],
            [
                'teamMember' => $ocramiusTeamMember,
                'isTeamMember' => true,
                'isMaintainer' => false,
                'projectSlug' => 'dbal',
                'project' => $project2,
                'github' => 'ocramius',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
            [
                'teamMember' => null,
                'isTeamMember' => false,
                'isMaintainer' => false,
                'projectSlug' => 'dbal',
                'project' => $project2,
                'github' => 'jim',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
        ], $rows);
    }
}
