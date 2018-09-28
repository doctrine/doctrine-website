<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use InvalidArgumentException;
use function assert;
use function is_string;
use function realpath;

class ProjectTest extends TestCase
{
    /** @var Project */
    private $project;

    protected function setUp() : void
    {
        $this->project = new Project([
            'name' => 'Test Project',
            'shortName' => 'Test Project',
            'slug' => 'test-project',
            'docsSlug' => 'doctrine-test-project',
            'composerPackageName' => 'doctrine/test-project',
            'repositoryName' => 'test-project',
            'docsRepositoryName' => 'test-project',
            'docsDir' => '/docs',
            'codePath' => '/src',
            'description' => 'Test description.',
            'keywords' => ['keyword1', 'keyword2'],
            'versions' => [
                [
                    'name' => 'master',
                    'branchName' => 'master',
                    'slug' => 'latest',
                ],
                [
                    'name' => '2.0',
                    'branchName' => '2.0',
                    'slug' => '2.0',
                    'current' => true,
                ],
                [
                    'name' => '1.0',
                    'branchName' => '1.0',
                    'slug' => '1.0',
                    'maintained' => false,
                ],
            ],
        ]);
    }

    public function testGetName() : void
    {
        self::assertSame('Test Project', $this->project->getName());
    }

    public function testGetShortName() : void
    {
        self::assertSame('Test Project', $this->project->getShortName());
    }

    public function testGetSlug() : void
    {
        self::assertSame('test-project', $this->project->getSlug());
    }

    public function testGetDocsSlug() : void
    {
        self::assertSame('doctrine-test-project', $this->project->getDocsSlug());
    }

    public function testGetComposerPackageName() : void
    {
        self::assertSame('doctrine/test-project', $this->project->getComposerPackageName());
    }

    public function testGetRepositoryName() : void
    {
        self::assertSame('test-project', $this->project->getRepositoryName());
    }

    public function testGetDocsRepositoryName() : void
    {
        self::assertSame('test-project', $this->project->getDocsRepositoryName());
    }

    public function testGetDocsPath() : void
    {
        self::assertSame('/docs', $this->project->getDocsPath());
    }

    public function testGetCodePath() : void
    {
        self::assertSame('/src', $this->project->getCodePath());
    }

    public function testGetDescription() : void
    {
        self::assertSame('Test description.', $this->project->getDescription());
    }

    public function testGetKeywords() : void
    {
        self::assertSame(['keyword1', 'keyword2'], $this->project->getKeywords());
    }

    public function testGetVersions() : void
    {
        self::assertCount(3, $this->project->getVersions());
    }

    public function testGetVersionsWithFilter() : void
    {
        $version = $this->project->getVersions(static function (ProjectVersion $version) {
            return $version->getSlug() === 'latest';
        })[0];

        self::assertSame('master', $version->getName());
    }

    public function testGetMaintainedVersions() : void
    {
        $maintainedVersions = $this->project->getMaintainedVersions();

        self::assertCount(2, $maintainedVersions);
    }

    public function testGetUnmaintainedVersions() : void
    {
        $unmaintainedVersions = $this->project->getUnmaintainedVersions();

        self::assertCount(1, $unmaintainedVersions);
    }

    public function testGetVersion() : void
    {
        $version = $this->project->getVersion('1.0');

        self::assertSame('1.0', $version->getName());
    }

    public function testGetVersionThrowsInvalidArgumentExceptionWithInvalidVersion() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not find version 10.0 for project test-project');

        $version = $this->project->getVersion('10.0');

        self::assertSame('1.0', $version->getName());
    }

    public function testGetCurrentVersion() : void
    {
        $version = $this->project->getCurrentVersion();

        self::assertInstanceOf(ProjectVersion::class, $version);

        self::assertSame('2.0', $version->getName());
    }

    public function testGetProjectDocsRepositoryPath() : void
    {
        self::assertSame('/test/test-project', $this->project->getProjectDocsRepositoryPath('/test'));
    }

    public function testGetProjectRepositoryPath() : void
    {
        self::assertSame('/test/test-project', $this->project->getProjectRepositoryPath('/test'));
    }

    public function testGetAbsoluteDocsPath() : void
    {
        $testProjectsPath = realpath(__DIR__ . '/../test-projects');

        assert(is_string($testProjectsPath));

        self::assertSame(
            $testProjectsPath . '/test-project/docs',
            $this->project->getAbsoluteDocsPath($testProjectsPath)
        );
    }
}
