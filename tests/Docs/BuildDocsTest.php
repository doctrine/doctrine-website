<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use DateTimeImmutable;
use Doctrine\Website\Docs\BuildDocs;
use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Git\Tag;
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
    /** @var ProjectRepository<Project>&MockObject  */
    private ProjectRepository&MockObject $projectRepository;

    private ProjectGitSyncer&MockObject $projectGitSyncer;

    private RSTBuilder&MockObject $rstBuilder;

    private SearchIndexer&MockObject $searchIndexer;

    private BuildDocs $buildDocs;

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

        $version = new ProjectVersion(['branchName' => '1.0']);
        $version->addDocsLanguage(new RSTLanguage('en', '/en'));

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
        $version->addDocsLanguage(new RSTLanguage('en', '/en'));
        $version->addTag(new Tag('1.0.1', new DateTimeImmutable('2000-01-01')));

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

        $version = new ProjectVersion([]);
        $version->addDocsLanguage(new RSTLanguage('en', '/en'));

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
