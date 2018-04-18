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

    /** @var array */
    private $crawled = [];

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
        $this->assertValid('/about/index.html');
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

            $this->assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
        }
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
