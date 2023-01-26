<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataBuilder;

use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\Website\DataBuilder\ContributorDataBuilder;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectContributorRepository;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContributorDataBuilderTest extends TestCase
{
    private ProjectContributorRepository&MockObject $projectContributorRepository;

    private ContributorDataBuilder $contributorDataBuilder;

    public function testBuild(): void
    {
        $project1 = $this->createProject(['slug' => 'dbal']);
        $project2 = $this->createProject(['slug' => 'orm']);

        $jwageTeamMember    = new TeamMember();
        $ocramiusTeamMember = new TeamMember();

        $objectManager = $this->createMock(ObjectManagerInterface::class);

        $projectContributor1 = $this->createProjectContributor([
            'github' => 'jwage',
            'teamMember' => $jwageTeamMember,
            'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=460&v=4',
            'numCommits' => 1,
            'numAdditions' => 1,
            'numDeletions' => 1,
            'project' => $project1,
        ]);

        $projectContributor2 = $this->createProjectContributor([
            'github' => 'jwage',
            'teamMember' => $jwageTeamMember,
            'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=460&v=4',
            'numCommits' => 1,
            'numAdditions' => 1,
            'numDeletions' => 1,
            'project' => $project2,
        ]);

        $projectContributor3 = $this->createProjectContributor([
            'github' => 'ocramius',
            'teamMember' => $ocramiusTeamMember,
            'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
            'numCommits' => 1,
            'numAdditions' => 1,
            'numDeletions' => 1,
            'project' => $project2,
        ]);

        $projectContributors = [$projectContributor1, $projectContributor2, $projectContributor3];

        $this->projectContributorRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projectContributors);

        $rows = $this->contributorDataBuilder->build()->getData();

        self::assertEquals([
            'jwage' => [
                'isTeamMember' => true,
                'github' => 'jwage',
                'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=460&v=4',
                'numCommits' => 2,
                'numAdditions' => 2,
                'numDeletions' => 2,
                'projects' => ['dbal', 'orm'],
            ],
            'ocramius' => [
                'isTeamMember' => true,
                'github' => 'ocramius',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 1,
                'numAdditions' => 1,
                'numDeletions' => 1,
                'projects' => ['orm'],
            ],
        ], $rows);
    }

    protected function setUp(): void
    {
        $this->projectContributorRepository = $this->createMock(ProjectContributorRepository::class);

        $this->contributorDataBuilder = new ContributorDataBuilder(
            $this->projectContributorRepository,
        );
    }
}
