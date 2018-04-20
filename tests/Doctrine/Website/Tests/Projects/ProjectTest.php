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
        $this->assertEquals('Object Relational Mapper', $this->project->getName());
    }

    public function testGetShortName() : void
    {
        $this->assertEquals('ORM', $this->project->getShortName());
    }

    public function testGetSlug() : void
    {
        $this->assertEquals('orm', $this->project->getSlug());
    }

    public function testGetDocsSlug() : void
    {
        $this->assertEquals('doctrine-orm', $this->project->getDocsSlug());
    }

    public function testGetComposerPackageName() : void
    {
        $this->assertEquals('doctrine/orm', $this->project->getComposerPackageName());
    }

    public function testGetRepositoryName() : void
    {
        $this->assertEquals('doctrine2', $this->project->getRepositoryName());
    }

    public function testHasDocs() : void
    {
        $this->assertTrue($this->project->hasDocs());
    }

    public function testGetDocsRepositoryName() : void
    {
        $this->assertEquals('doctrine2', $this->project->getDocsRepositoryName());
    }

    public function testGetDocsPath() : void
    {
        $this->assertEquals('/docs', $this->project->getDocsPath());
    }

    public function testGetCodePath() : void
    {
        $this->assertEquals('/src', $this->project->getCodePath());
    }

    public function testGetDescription() : void
    {
        $this->assertEquals('Test description.', $this->project->getDescription());
    }

    public function testGetKeywords() : void
    {
        $this->assertEquals(['keyword1', 'keyword2'], $this->project->getKeywords());
    }

    public function testGetVersions() : void
    {
        $this->assertCount(3, $this->project->getVersions());
    }

    public function testGetVersionsWithFilter() : void
    {
        $version = $this->project->getVersions(function (ProjectVersion $version) {
            return $version->getSlug() === 'latest';
        })[0];

        $this->assertEquals('master', $version->getName());
    }

    public function testGetMaintainedVersions() : void
    {
        $maintainedVersions = $this->project->getMaintainedVersions();

        $this->assertCount(2, $maintainedVersions);
    }

    public function testGetUnmaintainedVersions() : void
    {
        $unmaintainedVersions = $this->project->getUnmaintainedVersions();

        $this->assertCount(1, $unmaintainedVersions);
    }

    public function testGetVersion() : void
    {
        $version = $this->project->getVersion('1.0');

        $this->assertInstanceOf(ProjectVersion::class, $version);

        $this->assertEquals('1.0', $version->getName());
    }

    public function testGetCurrentVersion() : void
    {
        $version = $this->project->getCurrentVersion();

        $this->assertInstanceOf(ProjectVersion::class, $version);

        $this->assertEquals('2.0', $version->getName());
    }

    public function testGetProjectDocsRepositoryPath() : void
    {
        $this->assertEquals('/test/doctrine2', $this->project->getProjectDocsRepositoryPath('/test'));
    }

    public function testGetProjectRepositoryPath() : void
    {
        $this->assertEquals('/test/doctrine2', $this->project->getProjectRepositoryPath('/test'));
    }

    public function testGetAbsoluteDocsPath() : void
    {
        $this->assertEquals('/test/doctrine2/docs', $this->project->getAbsoluteDocsPath('/test'));
    }
}
