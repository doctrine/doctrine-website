<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Yaml\Yaml;
use function explode;
use function file_exists;
use function file_get_contents;
use function is_dir;
use function simplexml_load_string;
use function sprintf;

class FunctionalTest extends TestCase
{
    /** @var string */
    private $rootDir;

    /** @var string */
    private $buildDir;

    protected function setUp() : void
    {
        $this->rootDir  = __DIR__ . '/..';
        $this->buildDir = $this->rootDir . '/build-dev';

        if (is_dir($this->buildDir)) {
            return;
        }

        self::markTestSkipped('This test requires ./bin/console build-website to have been ran.');
    }

    public function testFunctional() : void
    {
        self::assertValid('/index.html');
        self::assertValid('/contribute/index.html');
        self::assertValid('/contribute/maintainer/index.html');
        self::assertValid('/contribute/website/index.html');
        self::assertValid('/community/index.html');
        self::assertValid('/blog/index.html');
        self::assertValid('/team/index.html');
        self::assertValid('/2018/04/06/new-website.html');
        self::assertValid('/projects.html');

        $data = Yaml::parseFile($this->rootDir . '/config/projects.yml');

        $projects = $data['parameters']['doctrine.website.projects'];

        foreach ($projects as $project) {
            // project homepage
            $crawler = self::assertValid(sprintf(
                '/projects/%s.html',
                $project['slug']
            ));

            self::assertSame($project['name'], $crawler->filter('h2')->text());
            self::assertSame($project['description'], $crawler->filter('p.lead')->text());

            foreach ($project['versions'] as $version) {
                // rst docs
                $crawler = self::assertValid(sprintf(
                    '/projects/%s/en/%s/index.html',
                    $project['docsSlug'],
                    $version['slug']
                ));

                self::assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
            }

            self::assertFileNotExists($this->getFullPath(sprintf(
                '/projects/%s/en/current/meta.php',
                $project['docsSlug']
            )));

            // rst docs current symlink
            $crawler = self::assertValid(sprintf(
                '/projects/%s/en/current/index.html',
                $project['docsSlug']
            ));

            // rst docs stable symlink
            $crawler = self::assertValid(sprintf(
                '/projects/%s/en/stable/index.html',
                $project['docsSlug']
            ));

            self::assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
        }
    }

    public function testLinks() : void
    {
        $crawler = self::assertValid('/projects/doctrine-orm/en/2.6/reference/events.html');
        self::assertContains('<a href="#reference-events-lifecycle-events">lifecycle events</a>', $crawler->html());

        $crawler = self::assertValid('/projects/doctrine-dbal/en/2.8/reference/data-retrieval-and-manipulation.html');
        self::assertContains('<a href="types.html#mappingMatrix">Types</a>', $crawler->html());

        $crawler = self::assertValid('/projects/doctrine-orm/en/2.6/reference/architecture.html');
        self::assertContains('<a href="../cookbook/implementing-wakeup-or-clone.html">do so safely</a>', $crawler->html());

        $crawler = self::assertValid('/projects/doctrine-orm/en/2.6/reference/annotations-reference.html');
        self::assertContains('<a href="annotations-reference.html#annref_column_result">@ColumnResult</a>', $crawler->html());

        $crawler = self::assertValid('/projects/doctrine-dbal/en/2.8/reference/events.html');
        self::assertContains('<a href="platforms.html">Platforms</a>', $crawler->html());

        $crawler = self::assertValid('/projects/doctrine-orm/en/2.6/reference/improving-performance.html');
        self::assertContains('<a href="../tutorials/extra-lazy-associations.html">tutorial</a>', $crawler->html());
    }

    public function testSitemap() : void
    {
        $sitemapPath = $this->getFullPath('/sitemap.xml');

        self::assertFileExists($sitemapPath);

        $xmlString = $this->getFileContents($sitemapPath);

        $lines = explode("\n", $xmlString);

        self::assertTrue(isset($lines[0]));
        self::assertSame('<?xml version="1.0" encoding="UTF-8"?>', $lines[0]);

        $xml = simplexml_load_string($xmlString);

        self::assertInstanceOf(SimpleXMLElement::class, $xml);
    }

    public function testAtom() : void
    {
        $atomPath = $this->getFullPath('/atom.xml');

        self::assertFileExists($atomPath);

        $xmlString = $this->getFileContents($atomPath);

        $lines = explode("\n", $xmlString);

        self::assertTrue(isset($lines[0]));
        self::assertSame('<?xml version="1.0" encoding="utf-8"?>', $lines[0]);

        $xml = simplexml_load_string($xmlString);

        self::assertInstanceOf(SimpleXMLElement::class, $xml);
    }

    public function testSearchBoxPlaceholder() : void
    {
        $crawler = $this->assertValid('/index.html');

        self::assertContains("placeholder: 'Search'", $crawler->html());

        $crawler = $this->assertValid('/projects/migrations.html');

        self::assertContains("placeholder: 'Search Migrations'", $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-migrations/en/1.7/index.html');

        self::assertContains("placeholder: 'Search Migrations 1.7'", $crawler->html());
    }

    public function testContribute() : void
    {
        $crawler = $this->assertValid('/contribute/index.html');

        self::assertContains('<a id="contribute"></a>', $crawler->html());
        self::assertContains('<h1>Contribute</h1>', $crawler->html());
    }

    public function testContributeMaintainer() : void
    {
        $crawler = $this->assertValid('/contribute/maintainer/index.html');

        self::assertContains('<a id="maintainer-workflow"></a>', $crawler->html());
        self::assertContains('<h1>Maintainer Workflow</h1>', $crawler->html());
    }

    public function testContributeWebsite() : void
    {
        $crawler = $this->assertValid('/contribute/website/index.html');

        self::assertContains('<a id="contribute-to-website"></a>', $crawler->html());
        self::assertContains('<h1>Contribute to Website</h1>', $crawler->html());
    }

    private function getFullPath(string $path) : string
    {
        $fullPath = $this->buildDir . $path;

        if (file_exists($fullPath)) {
            return $this->buildDir . $path;
        }

        return $this->buildDir . $path;
    }

    private function getFileContents(string $path) : string
    {
        $html = file_get_contents($path);

        if ($html === false) {
            self::fail(sprintf('File %s contents empty.', $path));
        }

        return $html;
    }

    private function assertValid(string $path) : Crawler
    {
        $fullPath = $this->getFullPath($path);

        $html = $this->getFileContents($fullPath);

        $crawler = new Crawler($html);

        self::assertCount(1, $crawler->filter('body'), sprintf('%s has a body', $path));

        return $crawler;
    }
}
