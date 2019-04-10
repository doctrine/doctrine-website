<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\BuildDocs;
use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDocsTest extends TestCase
{
    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var ProjectGitSyncer|MockObject */
    private $projectGitSyncer;

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
        $this->rstBuilder        = $this->createMock(RSTBuilder::class);
        $this->searchIndexer     = $this->createMock(SearchIndexer::class);

        $this->buildDocs = new BuildDocs(
            $this->projectRepository,
            $this->projectGitSyncer,
            $this->rstBuilder,
            $this->searchIndexer
        );
    }

    public function testBuild() : void
    {
        $output = $this->createMock(OutputInterface::class);

        $repositoryName = 'test-project';

        $project = $this->createMock(Project::class);

        $project->expects(self::any())
            ->method('getRepositoryName')
            ->willReturn($repositoryName);

        $version = $this->createMock(ProjectVersion::class);

        $projects = [$project];
        $versions = [$version];

        $project->expects(self::once())
            ->method('getVersions')
            ->willReturn($versions);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projects);

        $english = new RSTLanguage('en', '/en');

        $version->expects(self::once())
            ->method('getDocsLanguages')
            ->willReturn([$english]);

        $this->rstBuilder->expects(self::once())
            ->method('buildRSTDocs')
            ->with($project, $version, $english);

        $this->searchIndexer->expects(self::once())
            ->method('buildSearchIndexes')
            ->with($project, $version);

        $this->buildDocs->build($output, '', '', true);
    }
}
