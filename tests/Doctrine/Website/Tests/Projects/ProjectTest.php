<?php

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    /** @var Project */
    private $project;

    protected function setUp()
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
                    'name' => '1.0',
                    'branchName' => '1.0',
                    'slug' => '1.0',
                    'current' => true,
                ],
            ],
        ]);
    }

    public function testGetName()
    {
        $this->assertEquals('Object Relational Mapper', $this->project->getName());
    }

    public function testGetShortName()
    {
        $this->assertEquals('ORM', $this->project->getShortName());
    }

    public function testGetSlug()
    {
        $this->assertEquals('orm', $this->project->getSlug());
    }

    public function testGetDocsSlug()
    {
        $this->assertEquals('doctrine-orm', $this->project->getDocsSlug());
    }

    public function testGetComposerPackageName()
    {
        $this->assertEquals('doctrine/orm', $this->project->getComposerPackageName());
    }

    public function testGetRepositoryName()
    {
        $this->assertEquals('doctrine2', $this->project->getRepositoryName());
    }

    public function testHasDocs()
    {
        $this->assertTrue($this->project->hasDocs());
    }

    public function testGetDocsRepositoryName()
    {
        $this->assertEquals('doctrine2', $this->project->getDocsRepositoryName());
    }

    public function testGetDocsPath()
    {
        $this->assertEquals('/docs', $this->project->getDocsPath());
    }

    public function testGetCodePath()
    {
        $this->assertEquals('/src', $this->project->getCodePath());
    }

    public function testGetDescription()
    {
        $this->assertEquals('Test description.', $this->project->getDescription());
    }

    public function testGetKeywords()
    {
        $this->assertEquals(['keyword1', 'keyword2'], $this->project->getKeywords());
    }

    public function testGetVersions()
    {
        $this->assertCount(2, $this->project->getVersions());
    }

    public function testGetVersion()
    {
        $version = $this->project->getVersion('1.0');

        $this->assertInstanceOf(ProjectVersion::class, $version);

        $this->assertEquals('1.0', $version->getName());
    }

    public function testGetCurrentVersion()
    {
        $version = $this->project->getCurrentVersion();

        $this->assertInstanceOf(ProjectVersion::class, $version);

        $this->assertEquals('1.0', $version->getName());
    }

    public function testGetProjectDocsRepositoryPath()
    {
        $this->assertEquals('/test/doctrine2', $this->project->getProjectDocsRepositoryPath('/test'));
    }

    public function testGetProjectRepositoryPath()
    {
        $this->assertEquals('/test/doctrine2', $this->project->getProjectRepositoryPath('/test'));
    }

    public function testGetAbsoluteDocsPath()
    {
        $this->assertEquals('/test/doctrine2/docs', $this->project->getAbsoluteDocsPath('/test'));
    }
}
