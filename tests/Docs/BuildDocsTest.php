<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\BuildDocs;
use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

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

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectGitSyncer  = $this->createMock(ProjectGitSyncer::class);
        $this->rstBuilder        = $this->createMock(RSTBuilder::class);
        $this->searchIndexer     = $this->createMock(SearchIndexer::class);

        $this->buildDocs = new BuildDocs(
            $this->projectRepository,
            $this->projectGitSyncer,
            $this->rstBuilder,
            $this->searchIndexer,
        );
    }

    public function testBuildWithBranchCheckout(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $version = new ProjectVersion([
            'branchName'    => '1.0',
            'docsLanguages' => [
                [
                    'code'  => 'en',
                    'path'  => '/en',
                ],
            ],
        ]);

        $repositoryName = 'test-project';

        $project = $this->createMock(Project::class);

        $project->method('getRepositoryName')
            ->willReturn($repositoryName);

        $projects = [$project];
        $versions = [$version];

        $project->expects(self::once())
            ->method('getVersions')
            ->willReturn($versions);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projects);

        $this->projectGitSyncer->expects(self::once())
            ->method('checkoutBranch')
            ->with($repositoryName, '1.0');

        $english = $version->getDocsLanguages()[0];

        $this->rstBuilder->expects(self::once())
            ->method('buildRSTDocs')
            ->with($project, $version, $english);

        $this->searchIndexer->expects(self::once())
            ->method('buildSearchIndexes')
            ->with($project, $version);

        $this->buildDocs->build($output, '', '', true);
    }

    public function testBuildWithTagCheckout(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $version = new ProjectVersion([
            'tags'          => [
                [
                    'name' => '1.0.1',
                    'date' => '2000-01-01',
                ],
            ],
            'docsLanguages' => [
                [
                    'code' => 'en',
                    'path' => '/en',
                ],
            ],
        ]);

        $repositoryName = 'test-project';

        $project = $this->createMock(Project::class);

        $project->expects(self::any())
            ->method('getRepositoryName')
            ->willReturn($repositoryName);

        $projects = [$project];
        $versions = [$version];

        $project->expects(self::once())
            ->method('getVersions')
            ->willReturn($versions);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projects);

        $this->projectGitSyncer->expects(self::once())
            ->method('checkoutTag')
            ->with($repositoryName, '1.0.1');

        $english = $version->getDocsLanguages()[0];

        $this->rstBuilder->expects(self::once())
            ->method('buildRSTDocs')
            ->with($project, $version, $english);

        $this->searchIndexer->expects(self::once())
            ->method('buildSearchIndexes')
            ->with($project, $version);

        $this->buildDocs->build($output, '', '', true);
    }

    public function testBuildWithInvalidProjectVersion(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $version = new ProjectVersion([
            'docsLanguages' => [
                [
                    'code' => 'en',
                    'path' => '/en',
                ],
            ],
        ]);

        $repositoryName = 'test-project';

        $project = $this->createMock(Project::class);

        $project->expects(self::any())
            ->method('getRepositoryName')
            ->willReturn($repositoryName);

        $projects = [$project];
        $versions = [$version];

        $project->expects(self::once())
            ->method('getVersions')
            ->willReturn($versions);

        $this->projectRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($projects);

        $this->searchIndexer->expects(self::never())
            ->method('buildSearchIndexes');

        $this->expectException(UnexpectedValueException::class);
        $this->buildDocs->build($output, '', '', true);
    }
}
