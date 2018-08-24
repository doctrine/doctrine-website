<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\APIBuilder;
use Doctrine\Website\Docs\BuildDocs;
use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Projects\ProjectVersion;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDocsTest extends TestCase
{
    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var ProjectGitSyncer|MockObject */
    private $projectGitSyncer;

    /** @var APIBuilder|MockObject */
    private $apiBuilder;

    /** @var RSTBuilder|MockObject */
    private $rstBuilder;

    /** @var SearchIndexer|MockObject */
    private $searchIndexer;

    /** @var BuildDocs */
    private $buildDocs;

    protected function setUp() : void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectGitSyncer  = $this->createMock(ProjectGitSyncer::class);
        $this->apiBuilder        = $this->createMock(APIBuilder::class);
        $this->rstBuilder        = $this->createMock(RSTBuilder::class);
        $this->searchIndexer     = $this->createMock(SearchIndexer::class);

        $this->buildDocs = new BuildDocs(
            $this->projectRepository,
            $this->projectGitSyncer,
            $this->apiBuilder,
            $this->rstBuilder,
            $this->searchIndexer
        );
    }

    public function testBuild() : void
    {
        $output = $this->createMock(OutputInterface::class);

        $project = $this->createMock(Project::class);
        $version = $this->createMock(ProjectVersion::class);

        $projects = [$project];
        $versions = [$version];

        $project->expects(self::once())
            ->method('getVersions')
            ->willReturn($versions);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projects);

        $this->projectGitSyncer->expects(self::once())
            ->method('sync')
            ->with($project, $version);

        $this->apiBuilder->expects(self::once())
            ->method('buildAPIDocs')
            ->with($project, $version);

        $this->rstBuilder->expects(self::once())
            ->method('projectHasDocs')
            ->with($project)
            ->willReturn(true);

        $this->rstBuilder->expects(self::once())
            ->method('buildRSTDocs')
            ->with($project, $version);

        $this->searchIndexer->expects(self::once())
            ->method('buildSearchIndexes')
            ->with($project, $version);

        $this->buildDocs->build($output, '', '', true, true, true);
    }
}
