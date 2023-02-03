<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataBuilder;

use Doctrine\Website\DataBuilder\ProjectContributorDataBuilder;
use Doctrine\Website\Github\GithubProjectContributors;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectContributorDataBuilderTest extends TestCase
{
    /** @var ProjectRepository<Project>&MockObject */
    private ProjectRepository&MockObject $projectRepository;

    /** @var TeamMemberRepository<TeamMember>&MockObject */
    private TeamMemberRepository&MockObject $teamMemberRepository;

    private GithubProjectContributors&MockObject $githubProjectContributors;

    private ProjectContributorDataBuilder $projectContributorDataBuilder;

    public function testBuild(): void
    {
        $project1 = $this->createProject(['slug' => 'orm']);
        $project2 = $this->createProject(['slug' => 'dbal']);

        $jwageTeamMember = $this->createMock(TeamMember::class);

        $jwageTeamMember->method('isProjectMaintainer')
            ->willReturnMap([
                [$project1, true],
                [$project2, false],
            ]);

        $ocramiusTeamMember = $this->createMock(TeamMember::class);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([$project1, $project2]);

        $this->githubProjectContributors->expects(self::once())
            ->method('warmProjectsContributors')
            ->with([$project1, $project2]);

        $returnValueOfProject1 = [
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
        ];
        $returnValueOfProject2 = [
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
        ];
        $this->githubProjectContributors->expects(self::exactly(2))
            ->method('getProjectContributors')
            ->willReturnMap([
                [$project1, $returnValueOfProject1],
                [$project2, $returnValueOfProject2],
            ]);

        $this->teamMemberRepository->expects(self::exactly(6))
            ->method('findOneByGithub')
            ->willReturnMap([
                ['jwage', $jwageTeamMember],
                ['ocramius', $ocramiusTeamMember],
                ['bob', null],
                ['jim', null],
            ]);

        $rows = $this->projectContributorDataBuilder->build()->getData();

        self::assertEquals([
            [
                'isTeamMember' => true,
                'isMaintainer' => true,
                'projectSlug' => 'orm',
                'github' => 'jwage',
                'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=60&v=4',
                'numCommits' => 5,
                'numAdditions' => 3,
                'numDeletions' => 4,
            ],
            [
                'isTeamMember' => true,
                'isMaintainer' => false,
                'projectSlug' => 'orm',
                'github' => 'ocramius',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
            [
                'isTeamMember' => false,
                'isMaintainer' => false,
                'projectSlug' => 'orm',
                'github' => 'bob',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
            [
                'isTeamMember' => true,
                'isMaintainer' => false,
                'projectSlug' => 'dbal',
                'github' => 'jwage',
                'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=60&v=4',
                'numCommits' => 5,
                'numAdditions' => 3,
                'numDeletions' => 4,
            ],
            [
                'isTeamMember' => true,
                'isMaintainer' => false,
                'projectSlug' => 'dbal',
                'github' => 'ocramius',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
            [
                'isTeamMember' => false,
                'isMaintainer' => false,
                'projectSlug' => 'dbal',
                'github' => 'jim',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 10,
                'numAdditions' => 4,
                'numDeletions' => 5,
            ],
        ], $rows);
    }

    protected function setUp(): void
    {
        $this->projectRepository         = $this->createMock(ProjectRepository::class);
        $this->teamMemberRepository      = $this->createMock(TeamMemberRepository::class);
        $this->githubProjectContributors = $this->createMock(GithubProjectContributors::class);

        $this->projectContributorDataBuilder = new ProjectContributorDataBuilder(
            $this->projectRepository,
            $this->teamMemberRepository,
            $this->githubProjectContributors,
        );
    }
}
