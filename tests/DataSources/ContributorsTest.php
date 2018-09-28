<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\Website\DataSources\Contributors;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectContributor;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectContributorRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContributorsTest extends TestCase
{
    /** @var ProjectContributorRepository|MockObject */
    private $projectContributorRepository;

    /** @var Contributors */
    private $contributors;

    protected function setUp() : void
    {
        $this->projectContributorRepository = $this->createMock(ProjectContributorRepository::class);

        $this->contributors = new Contributors($this->projectContributorRepository);
    }

    public function testGetSourceRows() : void
    {
        $project1 = new Project([]);
        $project2 = new Project([]);

        $jwageTeamMember    = new TeamMember();
        $ocramiusTeamMember = new TeamMember();

        $objectManager = $this->createMock(ObjectManagerInterface::class);

        $projectContributor1 = new ProjectContributor();
        $projectContributor1->hydrate([
            'github' => 'jwage',
            'teamMember' => $jwageTeamMember,
            'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=460&v=4',
            'numCommits' => 1,
            'numAdditions' => 1,
            'numDeletions' => 1,
            'project' => $project1,
        ], $objectManager);

        $projectContributor2 = new ProjectContributor();
        $projectContributor2->hydrate([
            'github' => 'jwage',
            'teamMember' => $jwageTeamMember,
            'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=460&v=4',
            'numCommits' => 1,
            'numAdditions' => 1,
            'numDeletions' => 1,
            'project' => $project2,
        ], $objectManager);

        $projectContributor3 = new ProjectContributor();
        $projectContributor3->hydrate([
            'github' => 'ocramius',
            'teamMember' => $ocramiusTeamMember,
            'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
            'numCommits' => 1,
            'numAdditions' => 1,
            'numDeletions' => 1,
            'project' => $project2,
        ], $objectManager);

        $projectContributors = [$projectContributor1, $projectContributor2, $projectContributor3];

        $this->projectContributorRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projectContributors);

        $rows = $this->contributors->getSourceRows();

        self::assertEquals([
            'jwage' => [
                'teamMember' => $jwageTeamMember,
                'isTeamMember' => true,
                'github' => 'jwage',
                'avatarUrl' => 'https://avatars1.githubusercontent.com/u/97422?s=460&v=4',
                'numCommits' => 2,
                'numAdditions' => 2,
                'numDeletions' => 2,
                'projects' => [$project1, $project2],
            ],
            'ocramius' => [
                'teamMember' => $ocramiusTeamMember,
                'isTeamMember' => true,
                'github' => 'ocramius',
                'avatarUrl' => 'https://avatars0.githubusercontent.com/u/154256?s=460&v=4',
                'numCommits' => 1,
                'numAdditions' => 1,
                'numDeletions' => 1,
                'projects' => [$project2],
            ],
        ], $rows);
    }
}
