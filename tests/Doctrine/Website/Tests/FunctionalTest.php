<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Yaml\Yaml;
use function file_get_contents;
use function is_dir;
use function realpath;
use function sprintf;

class FunctionalTest extends TestCase
{
    /** @var string */
    private $rootDir;

    /** @var string */
    private $buildDir;

    protected function setUp() : void
    {
        $this->rootDir  = realpath(__DIR__ . '/../../../..');
        $this->buildDir = $this->rootDir . '/build-dev';

        if (is_dir($this->buildDir)) {
            return;
        }

        $this->markTestSkipped('This test requires ./doctrine build-website to have been ran.');
    }

    public function testFunctional() : void
    {
        $this->assertValid('/index.html');
        $this->assertValid('/contribute/index.html');
        $this->assertValid('/contribute/maintainer/index.html');
        $this->assertValid('/contribute/website/index.html');
        $this->assertValid('/community/index.html');
        $this->assertValid('/blog/index.html');
        $this->assertValid('/team/index.html');
        $this->assertValid('/2018/04/06/new-website.html');
        $this->assertValid('/projects.html');

        $data = Yaml::parse(file_get_contents($this->rootDir . '/app/config/projects.yml'));

        $projects = $data['parameters']['doctrine.projects'];

        foreach ($projects as $project) {
            // project homepage
            $crawler = $this->assertValid(sprintf(
                '/projects/%s.html',
                $project['slug']
            ));

            $this->assertEquals($project['name'], $crawler->filter('h2')->text());
            $this->assertEquals($project['description'], $crawler->filter('p.lead')->text());

            foreach ($project['versions'] as $version) {
                // rst docs
                $crawler = $this->assertValid(sprintf(
                    '/projects/%s/en/%s/index.html',
                    $project['docsSlug'],
                    $version['slug']
                ));

                $this->assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
            }

            // rst docs current symlink
            $crawler = $this->assertValid(sprintf(
                '/projects/%s/en/current/index.html',
                $project['docsSlug']
            ));

            // rst docs stable symlink
            $crawler = $this->assertValid(sprintf(
                '/projects/%s/en/stable/index.html',
                $project['docsSlug']
            ));

            $this->assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
        }
    }

    public function testLinks() : void
    {
        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/events.html');
        $this->assertContains('<a href="#reference-events-lifecycle-events">lifecycle events</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-dbal/en/2.8/reference/data-retrieval-and-manipulation.html');
        $this->assertContains('<a href="types.html#mappingMatrix">Types</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/architecture.html');
        $this->assertContains('<a href="../cookbook/implementing-wakeup-or-clone.html">do so safely</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/annotations-reference.html');
        $this->assertContains('<a href="annotations-reference.html#annref_column_result">@ColumnResult</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-dbal/en/2.8/reference/events.html');
        $this->assertContains('<a href="platforms.html">Platforms</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/improving-performance.html');
        $this->assertContains('<a href="../tutorials/extra-lazy-associations.html">tutorial</a>', $crawler->html());
    }

    private function assertValid(string $path) : Crawler
    {
        $fullPath = realpath($this->buildDir . $path);

        if (! $fullPath) {
            $this->fail(sprintf('Could not find file %s in the build.', $path));
        }

        $html = file_get_contents($fullPath);

        if (! $html) {
            $this->fail(sprintf('File %s contents empty.', $fullPath));
        }

        $crawler = new Crawler($html);

        $this->assertCount(1, $crawler->filter('body'), sprintf('%s has a body', $path));

        return $crawler;
    }
}
