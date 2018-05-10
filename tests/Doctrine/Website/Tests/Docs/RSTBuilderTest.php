<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\DoctrineSculpinBundle\Directive\TocDirective;
use Doctrine\Website\DoctrineSculpinBundle\Directive\TocHeaderDirective;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\RST\Kernel;
use Gregwar\RST\Builder;
use Gregwar\RST\HTML\Kernel as HTMLKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use function array_keys;
use function file_get_contents;
use function sprintf;

class RSTBuilderTest extends TestCase
{
    /** @var string */
    private $sculpinSourcePath;

    /** @var Builder */
    private $builder;

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $projectsPath;

    /** @var RSTBuilder */
    private $rstBuilder;

    protected function setUp() : void
    {
        $this->sculpinSourcePath = __DIR__ . '/resources/sculpin-source';
        $this->projectsPath      = __DIR__ . '/resources';
        $this->builder           = new Builder(new Kernel(new HTMLKernel(), [
            new TocDirective(),
            new TocHeaderDirective(),
        ]));
        $this->filesystem        = new Filesystem();

        $this->rstBuilder = new RSTBuilder(
            $this->sculpinSourcePath,
            $this->builder,
            $this->filesystem,
            $this->projectsPath
        );

        $project = new Project([
            'docsSlug' => 'example-project',
            'docsRepositoryName' => 'example-project',
            'docsPath' => '/docs',
        ]);

        $version = new ProjectVersion(['slug' => '1.0']);

        $this->rstBuilder->buildRSTDocs($project, $version);
    }

    public function testGetDocuments() : void
    {
        $this->assertCount(7, $this->rstBuilder->getDocuments());
        $this->assertEquals([
            'index',
            'about',
            'cookbook/article',
            'cookbook/nested/nested',
            'cross-ref',
            'example',
            'reference/getting-started',
        ], array_keys($this->rstBuilder->getDocuments()));
    }

    public function testProjectHasDocs() : void
    {
        $project = new Project([
            'docsRepositoryName' => 'example-project',
            'docsPath' => '/docs',
        ]);

        $this->assertTrue($this->rstBuilder->projectHasDocs($project));
    }

    public function testFilesExist() : void
    {
        foreach (array_keys($this->rstBuilder->getDocuments()) as $file) {
            $this->assertSculpinSourceFileExists(sprintf(
                '/projects/example-project/en/1.0/%s.html',
                $file
            ));
        }
    }

    public function testH1Anchors() : void
    {
        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<a class="section-anchor" id="index" name="index"></a><h1 class="section-header"><a href="#index">Index<i class="fas fa-link"></i></a></h1>'
        );
    }

    public function testReferences() : void
    {
        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="about.html">About1</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="about.html">About2</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="example.html">Example</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_anchor">Cross Ref</a></li>'
        );

        $expected = <<<TOC
<p><a href="../reference/getting-started.html">Getting Started</a></p>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/article.html',
            $expected
        );

        $expected = <<<TOC
<p><a href="../cookbook/nested/nested.html">Nested Reference</a></p>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/article.html',
            $expected
        );

        $expected = <<<TOC
<p><a href="../reference/getting-started.html">Getting Started</a></p>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/article.html',
            $expected
        );

        $expected = <<<TOC
<p><a href="../reference/getting-started.html">Getting Started</a></p>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/article.html',
            $expected
        );
    }

    public function testExternalLink() : void
    {
        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="https://www.doctrine-project.org">TestLink</a></li>'
        );
    }

    public function testLists() : void
    {
        $expected = <<<HTML
<ul><li class="dash">List item 1
multiline</li>
<li class="dash">List item 2</li>
<li class="dash">List item 3
multiline</li>
</ul>
HTML;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            $expected
        );

        $expected = <<<HTML
<ul><li class="dash">
    Alternate list item 1
    multiline</li>
<li class="dash">
    Alternate list item 2</li>
<li class="dash">
    Alternate list item 3
    multiline</li>
</ul>
HTML;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            $expected
        );
    }

    public function testAnchors() : void
    {
        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<a id="lists"></a>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<p><a href="#lists">go to lists</a></p>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<p><a href="#anchor-section">@Anchor Section</a></p>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<ul><li class="dash"> <a href="#test_reference_anchor">@Test Reference Anchor</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_section_1_anchor">Cross Ref Section 1</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_section_2_anchor">Cross Ref Section 2</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_section_a_anchor">Cross Ref Section A</a></li>'
        );
    }

    public function testToc() : void
    {
        $expected = <<<TOC
<h2 class="toc-header">Glob TOC Title</h2>
<div class="toc-section"></div>
<div class="toc"><ul><li id="about-html-about" class="toc-item"><a href="about.html#about">About</a></li><ul><li id="about-html-section" class="toc-item"><a href="about.html#section">Section</a></li></ul><li id="cookbook-article-html-cookbook-article" class="toc-item"><a href="cookbook/article.html#cookbook-article">Cookbook Article</a></li><li id="cookbook-nested-nested-html-nested-cookbook" class="toc-item"><a href="cookbook/nested/nested.html#nested-cookbook">Nested Cookbook</a></li><li id="cross-ref-html-cross-ref" class="toc-item"><a href="cross-ref.html#cross-ref">Cross Ref</a></li><ul><li id="cross-ref-html-cross-ref-section-1" class="toc-item"><a href="cross-ref.html#cross-ref-section-1">Cross Ref Section 1</a></li><li id="cross-ref-html-cross-ref-section-2" class="toc-item"><a href="cross-ref.html#cross-ref-section-2">Cross Ref Section 2</a></li><ul><li id="cross-ref-html-cross-ref-section-a" class="toc-item"><a href="cross-ref.html#cross-ref-section-a">Cross Ref Section A</a></li></ul></ul><li id="example-html-example" class="toc-item"><a href="example.html#example">Example</a></li><ul><li id="example-html-section" class="toc-item"><a href="example.html#section">Section</a></li></ul><li id="index-html-index" class="toc-item"><a href="index.html#index">Index</a></li><ul><li id="index-html-section" class="toc-item"><a href="index.html#section">Section</a></li><li id="index-html-lists" class="toc-item"><a href="index.html#lists">Lists</a></li><li id="index-html-alternate-list-syntax" class="toc-item"><a href="index.html#alternate-list-syntax">Alternate List Syntax</a></li><li id="index-html-anchors" class="toc-item"><a href="index.html#anchors">Anchors</a></li><li id="index-html-anchor-section" class="toc-item"><a href="index.html#anchor-section">@Anchor Section</a></li><li id="index-html-anchors" class="toc-item"><a href="index.html#anchors">Anchors</a></li><li id="index-html-links" class="toc-item"><a href="index.html#links">Links</a></li><li id="index-html-reference-anchor" class="toc-item"><a href="index.html#reference-anchor">Reference Anchor</a></li><li id="index-html-glob-toc" class="toc-item"><a href="index.html#glob-toc">Glob TOC</a></li><li id="index-html-toc" class="toc-item"><a href="index.html#toc">TOC</a></li><li id="index-html-folder" class="toc-item"><a href="index.html#folder">Folder</a></li></ul><li id="reference-getting-started-html-getting-started" class="toc-item"><a href="reference/getting-started.html#getting-started">Getting Started</a></li></ul></div>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            $expected
        );

        $expected = <<<TOC
<h2 class="toc-header">TOC Title</h2>
<div class="toc-section"></div>
<div class="toc"><ul><li id="about-html-about" class="toc-item"><a href="about.html#about">About</a></li><ul><li id="about-html-section" class="toc-item"><a href="about.html#section">Section</a></li></ul><li id="cross-ref-html-cross-ref" class="toc-item"><a href="cross-ref.html#cross-ref">Cross Ref</a></li><ul><li id="cross-ref-html-cross-ref-section-1" class="toc-item"><a href="cross-ref.html#cross-ref-section-1">Cross Ref Section 1</a></li><li id="cross-ref-html-cross-ref-section-2" class="toc-item"><a href="cross-ref.html#cross-ref-section-2">Cross Ref Section 2</a></li><ul><li id="cross-ref-html-cross-ref-section-a" class="toc-item"><a href="cross-ref.html#cross-ref-section-a">Cross Ref Section A</a></li></ul></ul><li id="example-html-example" class="toc-item"><a href="example.html#example">Example</a></li><ul><li id="example-html-section" class="toc-item"><a href="example.html#section">Section</a></li></ul><li id="index-html-index" class="toc-item"><a href="index.html#index">Index</a></li><ul><li id="index-html-section" class="toc-item"><a href="index.html#section">Section</a></li><li id="index-html-lists" class="toc-item"><a href="index.html#lists">Lists</a></li><li id="index-html-alternate-list-syntax" class="toc-item"><a href="index.html#alternate-list-syntax">Alternate List Syntax</a></li><li id="index-html-anchors" class="toc-item"><a href="index.html#anchors">Anchors</a></li><li id="index-html-anchor-section" class="toc-item"><a href="index.html#anchor-section">@Anchor Section</a></li><li id="index-html-anchors" class="toc-item"><a href="index.html#anchors">Anchors</a></li><li id="index-html-links" class="toc-item"><a href="index.html#links">Links</a></li><li id="index-html-reference-anchor" class="toc-item"><a href="index.html#reference-anchor">Reference Anchor</a></li><li id="index-html-glob-toc" class="toc-item"><a href="index.html#glob-toc">Glob TOC</a></li><li id="index-html-toc" class="toc-item"><a href="index.html#toc">TOC</a></li><li id="index-html-folder" class="toc-item"><a href="index.html#folder">Folder</a></li></ul></ul></div>
<a class="section-anchor" id="folder" name="folder"></a><h2 class="section-header"><a href="#folder">Folder<i class="fas fa-link"></i></a></h2>
<ul><li class="dash"><a href="reference/getting-started.html">Getting Started</a></li>
</ul>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            $expected
        );

        $expected = <<<TOC
<div class="toc"><ul><li id="reference-getting-started-html-getting-started" class="toc-item"><a href="../reference/getting-started.html#getting-started">Getting Started</a></li><li id="article-html-cookbook-article" class="toc-item"><a href="article.html#cookbook-article">Cookbook Article</a></li><li id="cookbook-nested-nested-html-nested-cookbook" class="toc-item"><a href="../cookbook/nested/nested.html#nested-cookbook">Nested Cookbook</a></li><li id="about-html-about" class="toc-item"><a href="../about.html#about">About</a></li><ul><li id="about-html-section" class="toc-item"><a href="../about.html#section">Section</a></li></ul><li id="example-html-example" class="toc-item"><a href="../example.html#example">Example</a></li><ul><li id="example-html-section" class="toc-item"><a href="../example.html#section">Section</a></li></ul></ul></div>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/article.html',
            $expected
        );

        $expected = <<<TOC
<p><a href="../../about.html">Test About</a></p>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/nested/nested.html',
            $expected
        );

        $expected = <<<TOC
<div class="toc"><ul><li id="about-html-about" class="toc-item"><a href="../../about.html#about">About</a></li><ul></ul></ul></div>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/nested/nested.html',
            $expected
        );

        $expected = <<<TOC
<div class="toc"><ul><li id="cookbook-nested-nested-html-nested-cookbook" class="toc-item"><a href="../cookbook/nested/nested.html#nested-cookbook">Nested Cookbook</a></li></ul></div>
TOC;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/cookbook/article.html',
            $expected
        );
    }

    private function assertSculpinSourceFileExists(string $path) : void
    {
        $this->assertFileExists($this->sculpinSourcePath . $path);
    }

    private function assertSculpinSourceFileContains(string $path, string $contains) : void
    {
        $html = file_get_contents($this->sculpinSourcePath . $path);

        $this->assertContains($contains, $html);
    }
}
