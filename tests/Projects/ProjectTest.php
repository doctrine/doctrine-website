<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    /** @var Project */
    private $project;

    protected function setUp() : void
    {
        $this->project = new Project([
            'name' => 'Object Relational Mapper',
            'shortName' => 'ORM',
            'slug' => 'orm',
            'docsSlug' => 'doctrine-orm',
            'composerPackageName' => 'doctrine/orm',
            'repositoryName' => 'doctrine2',
            'docsRepositoryName' => 'doctrine2',
            'docsPath' => '/docs',
            'codePath' => '/src',
            'hasDocs' => true,
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
        self::assertSame('Object Relational Mapper', $this->project->getName());
    }

    public function testGetShortName() : void
    {
        self::assertSame('ORM', $this->project->getShortName());
    }

    public function testGetSlug() : void
    {
        self::assertSame('orm', $this->project->getSlug());
    }

    public function testGetDocsSlug() : void
    {
        self::assertSame('doctrine-orm', $this->project->getDocsSlug());
    }

    public function testGetComposerPackageName() : void
    {
        self::assertSame('doctrine/orm', $this->project->getComposerPackageName());
    }

    public function testGetRepositoryName() : void
    {
        self::assertSame('doctrine2', $this->project->getRepositoryName());
    }

    public function testHasDocs() : void
    {
        self::assertTrue($this->project->hasDocs());
    }

    public function testGetDocsRepositoryName() : void
    {
        self::assertSame('doctrine2', $this->project->getDocsRepositoryName());
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
        $version = $this->project->getVersions(function (ProjectVersion $version) {
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

        self::assertInstanceOf(ProjectVersion::class, $version);

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
        self::assertSame('/test/doctrine2', $this->project->getProjectDocsRepositoryPath('/test'));
    }

    public function testGetProjectRepositoryPath() : void
    {
        self::assertSame('/test/doctrine2', $this->project->getProjectRepositoryPath('/test'));
    }

    public function testGetAbsoluteDocsPath() : void
    {
        self::assertSame('/test/doctrine2/docs', $this->project->getAbsoluteDocsPath('/test'));
    }
}
