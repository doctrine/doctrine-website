<?php

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\APIBuilder;
use Doctrine\Website\Docs\BuildDocs;
use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\Projects\ProjectVersions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDocsTest extends TestCase
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ProjectGitSyncer */
    private $projectGitSyncer;

    /** @var APIBuilder */
    private $apiBuilder;

    /** @var RSTBuilder */
    private $rstBuilder;

    /** @var SearchIndexer */
    private $searchIndexer;

    /** @var BuildDocs */
    private $buildDocs;

    protected function setUp()
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectGitSyncer = $this->createMock(ProjectGitSyncer::class);
        $this->apiBuilder = $this->createMock(APIBuilder::class);
        $this->rstBuilder = $this->createMock(RSTBuilder::class);
        $this->searchIndexer = $this->createMock(SearchIndexer::class);

        $this->buildDocs = new BuildDocs(
            $this->projectRepository,
            $this->projectGitSyncer,
            $this->apiBuilder,
            $this->rstBuilder,
            $this->searchIndexer
        );
    }

    public function testBuild()
    {
        $output = $this->createMock(OutputInterface::class);

        $project = $this->createMock(Project::class);
        $version = $this->createMock(ProjectVersion::class);

        $projects = [$project];
        $versions = new ProjectVersions([$version]);

        $project->expects($this->once())
            ->method('getVersions')
            ->willReturn($versions);

        $this->projectRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($projects);

        $this->projectGitSyncer->expects($this->once())
            ->method('sync')
            ->with($project, $version);

        $this->apiBuilder->expects($this->once())
            ->method('buildAPIDocs')
            ->with($project, $version);

        $this->rstBuilder->expects($this->once())
            ->method('projectHasDocs')
            ->with($project)
            ->willReturn(true);

        $this->rstBuilder->expects($this->once())
            ->method('buildRSTDocs')
            ->with($project, $version);

        $this->searchIndexer->expects($this->once())
            ->method('buildSearchIndexes')
            ->with($project, $version);

        $this->buildDocs->build($output, '', '', true, true);
    }
}
