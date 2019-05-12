<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataBuilder\ContributorDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteData;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\DataSources\Contributors;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContributorsTest extends TestCase
{
    /** @var WebsiteDataReader|MockObject */
    private $dataReader;

    /** @var TeamMemberRepository|MockObject */
    private $teamMemberRepository;

    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var Contributors */
    private $contributors;

    protected function setUp() : void
    {
        $this->dataReader           = $this->createMock(WebsiteDataReader::class);
        $this->teamMemberRepository = $this->createMock(TeamMemberRepository::class);
        $this->projectRepository    = $this->createMock(ProjectRepository::class);

        $this->contributors = new Contributors(
            $this->dataReader,
            $this->teamMemberRepository,
            $this->projectRepository
        );
    }

    public function testGetSourceRows() : void
    {
        $projectContributors = [
            [
                'github' => 'jwage',
                'projects' => ['orm'],
            ],
            [
                'github' => 'Ocramius',
                'projects' => ['dbal'],
            ],
        ];

        $jwageTeamMember    = $this->createMock(TeamMember::class);
        $ocramiusTeamMember = $this->createMock(TeamMember::class);

        $ormProject  = $this->createMock(Project::class);
        $dbalProject = $this->createMock(Project::class);

        $this->dataReader->expects(self::once())
            ->method('read')
            ->with(ContributorDataBuilder::DATA_FILE)
            ->willReturn(new WebsiteData(ContributorDataBuilder::DATA_FILE, $projectContributors));

        $this->teamMemberRepository->expects(self::at(0))
            ->method('findOneByGithub')
            ->with('jwage')
            ->willReturn($jwageTeamMember);

        $this->projectRepository->expects(self::at(0))
            ->method('findOneBySlug')
            ->with('orm')
            ->willReturn($ormProject);

        $this->teamMemberRepository->expects(self::at(1))
            ->method('findOneByGithub')
            ->with('Ocramius')
            ->willReturn($ocramiusTeamMember);

        $this->projectRepository->expects(self::at(1))
            ->method('findOneBySlug')
            ->with('dbal')
            ->willReturn($dbalProject);

        $rows = $this->contributors->getSourceRows();

        self::assertEquals([
            [
                'github' => 'jwage',
                'projects' => [$ormProject],
                'teamMember' => $jwageTeamMember,
            ],
            [
                'github' => 'Ocramius',
                'projects' => [$dbalProject],
                'teamMember' => $ocramiusTeamMember,
            ],
        ], $rows);
    }
}
