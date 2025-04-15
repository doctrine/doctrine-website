<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources\DbPrefill;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\DataSources\DataSource;
use Doctrine\Website\DataSources\DbPrefill\Projects;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectStats;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;

use function assert;
use function file_get_contents;
use function is_dir;
use function json_decode;

class ProjectsTest extends TestCase
{
    protected function setUp(): void
    {
        $buildDir = __DIR__ . '/../../../build-test';

        if (is_dir($buildDir)) {
            return;
        }

        self::markTestSkipped('This test requires ./bin/console build-website to have been run.');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $entityManager = $this->getEntityManager();
        $repository    = $entityManager->getRepository(Project::class);
        $project       = $repository->find('testproject');

        assert($project instanceof Project);

        $entityManager->remove($project);
        $entityManager->flush();
    }

    public function testPopulate(): void
    {
        $projectFixture = __DIR__ . '/fixtures/projects.json';
        $fixture        = json_decode((string) file_get_contents($projectFixture), true);

        $entityManager = $this->getEntityManager();

        $dataSource = $this->createMock(DataSource::class);
        $dataSource->method('getSourceRows')->willReturn($fixture);

        $dbFill = new Projects($dataSource, $entityManager);
        $dbFill->populate();

        $this->assertProjectIsComplete($entityManager);
    }

    private function assertProjectIsComplete(EntityManagerInterface $entityManager): void
    {
        $entityManager->clear();

        $repository = $entityManager->getRepository(Project::class);
        $project    = $repository->find('testproject');

        assert($project instanceof Project);

        self::assertSame('Testproject', $project->getName());
        self::assertSame('testproject', $project->getSlug());
        self::assertSame('Testproject', $project->getShortName());
        self::assertTrue($project->isActive());
        self::assertFalse($project->isArchived());
        self::assertSame('doctrine-testproject', $project->getDocsSlug());
        self::assertSame('testproject', $project->getDocsRepositoryName());
        self::assertSame('/docs', $project->getDocsPath());
        self::assertSame('doctrine/testproject', $project->getComposerPackageName());
        self::assertSame('testproject', $project->getRepositoryName());
        self::assertFalse($project->isIntegration());
        self::assertSame('', $project->getIntegrationFor());
        self::assertSame('It\'s a testproject', $project->getDescription());
        self::assertSame(['testproject', 'docblock', 'parser'], $project->getKeywords());
        self::assertSame(42, $project->sortOrder);
        $this->assertProjectStats($project->getProjectStats());

        $versions = $project->getVersions();
        self::assertCount(1, $versions);
        $this->assertVersion($versions[0]);
    }

    private function assertVersion(ProjectVersion $version): void
    {
        self::assertSame('2.0', $version->getName());
        self::assertSame('2.0.x', $version->getBranchName());
        self::assertSame('2.0', $version->getSlug());
        self::assertTrue($version->isCurrent());
        self::assertTrue($version->isMaintained());
        self::assertTrue($version->hasDocs());
        self::assertSame(['foo', 'current', 'stable'], $version->getAliases());

        self::assertCount(1, $version->getTags());
        $this->assertTag($version->getTag('2.0.0'));

        $docsLanguages = $version->getDocsLanguages();
        self::assertCount(1, $docsLanguages);
        $this->assertDocsLanguage($docsLanguages[0]);
    }

    private function assertTag(Tag $tag): void
    {
        self::assertSame('2.0.0', $tag->getName());
        self::assertSame('2.0.0', $tag->getDisplayName());
        self::assertSame('2.0.0', $tag->getSlug());
        self::assertSame(1671492263, $tag->getDate()->getTimestamp());
    }

    private function assertDocsLanguage(RSTLanguage $docsLanguage): void
    {
        self::assertSame('en', $docsLanguage->getCode());
        self::assertSame('projects/testproject/docs/en', $docsLanguage->getPath());
    }

    private function assertProjectStats(ProjectStats $projectStats): void
    {
        self::assertSame(6729, $projectStats->getGithubStars());
        self::assertSame(41, $projectStats->getGithubWatchers());
        self::assertSame(236, $projectStats->getGithubForks());
        self::assertSame(30, $projectStats->getGithubOpenIssues());
        self::assertSame(2349, $projectStats->getDependents());
        self::assertSame(74, $projectStats->getSuggesters());
        self::assertSame(426454499, $projectStats->getTotalDownloads());
        self::assertSame(6487217, $projectStats->getMonthlyDownloads());
        self::assertSame(260080, $projectStats->getDailyDownloads());
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        assert($entityManager instanceof EntityManagerInterface);

        return $entityManager;
    }
}
