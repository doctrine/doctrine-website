<?php

namespace Doctrine\Website\Tests\Docs;

use Gregwar\RST\HTML\Kernel as HTMLKernel;
use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\RST\Builder;
use Doctrine\Website\SculpinRstBundle\Kernel\Kernel as SculpinKernel;
use PHPUnit\Framework\TestCase;

class RSTBuilderTest extends TestCase
{
    /** @var string */
    private $sculpinSourcePath;

    /** @var Builder */
    private $builder;

    /** @var string */
    private $projectsPath;

    /** @var RSTBuilder */
    private $rstBuilder;

    protected function setUp()
    {
        $this->sculpinSourcePath = __DIR__.'/resources/sculpin-source';
        $this->projectsPath = __DIR__.'/resources';
        $this->builder = new Builder(new SculpinKernel(new HTMLKernel(), []));

        $this->rstBuilder = new RSTBuilder(
            $this->sculpinSourcePath,
            $this->builder,
            $this->projectsPath
        );
    }

    public function testGetDocuments()
    {
        $this->assertEquals([], $this->rstBuilder->getDocuments());
    }

    public function testProjectHasDocs()
    {
        $project = new Project([
            'docsRepositoryName' => 'example-project',
            'docsPath' => '/docs',
        ]);

        $this->assertTrue($this->rstBuilder->projectHasDocs($project));
    }

    public function testBuildRSTDocs()
    {
        $project = new Project([
            'docsSlug' => 'example-project',
            'docsRepositoryName' => 'example-project',
            'docsPath' => '/docs',
        ]);

        $version = new ProjectVersion([
            'slug' => '1.0',
        ]);

        $this->rstBuilder->buildRSTDocs($project, $version);

        $this->assertSculpinSourceFileExists('/projects/example-project/en/1.0/index.html');
        $this->assertSculpinSourceFileExists('/projects/example-project/en/1.0/about.html');
        $this->assertSculpinSourceFileExists('/projects/example-project/en/1.0/example.html');

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<a class="section-anchor" id="title.1" name="title.1"></a><h1 class="section-header"><a href="#title.1">Index<i class="fas fa-link"></i></a></h1>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="about.html">About</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="example.html">Example</a></li>'
        );
    }

    private function assertSculpinSourceFileExists(string $path)
    {
        $this->assertFileExists($this->sculpinSourcePath.$path);
    }

    private function assertSculpinSourceFileContains(string $path, string $contains)
    {
        $this->assertContains($contains, file_get_contents($this->sculpinSourcePath.$path));
    }
}
