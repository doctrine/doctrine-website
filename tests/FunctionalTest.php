<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use SimpleXMLElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;

use function array_map;
use function end;
use function explode;
use function file_exists;
use function file_get_contents;
use function is_dir;
use function simplexml_load_string;
use function sprintf;

class FunctionalTest extends TestCase
{
    private string $rootDir;

    private string|null $buildDir = null;

    protected function setUp(): void
    {
        $this->rootDir  = __DIR__ . '/..';
        $this->buildDir = $this->getBuildDir();

        if ($this->buildDir !== null) {
            return;
        }

        self::markTestSkipped('This test requires ./bin/console build-website to have been ran.');
    }

    public function testProjectVersionsAndTags(): void
    {
        $container = $this->getContainer();

        $projectRepository = $container->get(ProjectRepository::class);
        self::assertInstanceOf(ProjectRepository::class, $projectRepository);

        $project = $projectRepository->findOneBySlug('orm');

        $versions = $project->getVersions();

        $firstVersion = end($versions);
        self::assertInstanceOf(ProjectVersion::class, $firstVersion);

        self::assertSame('2.0', $firstVersion->getName());

        $tags = $firstVersion->getTags();

        self::assertCount(14, $tags);

        self::assertSame('2.0.7', $firstVersion->getLatestTag()->getName());
        self::assertSame('2.0.0-BETA1', $firstVersion->getFirstTag()->getName());
    }

    public function testHomepageEditLink(): void
    {
        $crawler = $this->assertValid('/index.html');

        $editLink = $crawler->filter('.layout-edit-button a')->last();

        self::assertSame('Edit', $editLink->text());

        self::assertSame(
            'https://github.com/doctrine/doctrine-website/edit/master/source/index.html',
            $editLink->attr('href'),
        );
    }

    public function testProjectEditLink(): void
    {
        $crawler = $this->assertValid('/projects/annotations.html');

        $editLink = $crawler->filter('.layout-edit-button a')->last();

        self::assertSame('Edit', $editLink->text());

        self::assertSame(
            'https://github.com/doctrine/doctrine-website/edit/master/source/projects/annotations.html',
            $editLink->attr('href'),
        );
    }

    public function testDocumentationEditLink(): void
    {
        $crawler = $this->assertValid('/projects/doctrine-annotations/en/1.6/custom.html');

        $editLink = $crawler->filter('.layout-edit-button a')->last();

        self::assertSame('Edit', $editLink->text());

        self::assertSame(
            'https://github.com/doctrine/annotations/edit/1.6/docs/en/custom.rst',
            $editLink->attr('href'),
        );
    }

    public function testDocumentationPageBreadcrumbs(): void
    {
        $crawler = $this->assertValid('/projects/doctrine-annotations/en/1.6/custom.html');

        $lastLi = $crawler->filter('.breadcrumbs ol li')->last();

        self::assertSame('Custom Annotation Classes', $lastLi->text());
    }

    public function testHomepageWhoUsesDoctrine(): void
    {
        $crawler = $this->assertValid('/index.html');

        $table = $crawler->filter('#who-uses-doctrine-table a');

        $parsedDoctrineUsers = array_map(static function (Link $link): array {
            return ['name' => $link->getNode()->textContent, 'url' => $link->getUri()];
        }, $table->links());

        $expectedDoctrineUsers = $this->getContainer()->getParameter('doctrine.website.doctrine_users');

        self::assertSame($expectedDoctrineUsers, $parsedDoctrineUsers);
    }

    public function testFunctional(): void
    {
        $this->assertValid('/contribute/index.html');
        $this->assertValid('/contribute/maintainer/index.html');
        $this->assertValid('/contribute/website/index.html');
        $this->assertValid('/community/index.html');
        $this->assertValid('/blog/index.html');
        $this->assertValid('/team/maintainers.html');
        $this->assertValid('/team/contributors.html');
        $this->assertValid('/2018/04/06/new-website.html');
        $this->assertValid('/projects.html');

        $container = $this->getContainer();

        $projectRepository = $container->get(ProjectRepository::class);
        self::assertInstanceOf(ProjectRepository::class, $projectRepository);

        /** @var Project[] $projects */
        $projects = $projectRepository->findAll();

        foreach ($projects as $project) {
            // project homepage
            $crawler = $this->assertValid(sprintf(
                '/projects/%s.html',
                $project->getSlug(),
            ));

            self::assertSame($project->getName(), $crawler->filter('h2')->text());
            self::assertSame($project->getDescription(), $crawler->filter('p.lead')->text());

            foreach ($project->getVersions() as $version) {
                if (! $version->hasDocs()) {
                    continue;
                }

                // rst docs
                $crawler = $this->assertValid(sprintf(
                    '/projects/%s/en/%s/index.html',
                    $project->getDocsSlug(),
                    $version->getSlug(),
                ));

                self::assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
            }

            self::assertFileDoesNotExist($this->getFullPath(sprintf(
                '/projects/%s/en/current/meta.php',
                $project->getDocsSlug(),
            )));

            // rst docs current symlink
            $crawler = $this->assertValid(sprintf(
                '/projects/%s/en/current/index.html',
                $project->getDocsSlug(),
            ));

            self::assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));

            // rst docs stable symlink
            $crawler = $this->assertValid(sprintf(
                '/projects/%s/en/stable/index.html',
                $project->getDocsSlug(),
            ));

            self::assertCount(3, $crawler->filter('nav.breadcrumbs ol.breadcrumb li.breadcrumb-item'));
        }
    }

    public function testLinks(): void
    {
        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/events.html');
        self::assertStringContainsString('<a href="events.html#reference-events-lifecycle-events">lifecycle events</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-dbal/en/2.8/reference/data-retrieval-and-manipulation.html');
        self::assertStringContainsString('<a href="types.html#mappingMatrix">Types</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/architecture.html');
        self::assertStringContainsString('<a href="../cookbook/implementing-wakeup-or-clone.html">do so safely</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/annotations-reference.html');
        self::assertStringContainsString('<a href="annotations-reference.html#annref_column_result">@ColumnResult</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-dbal/en/2.8/reference/events.html');
        self::assertStringContainsString('<a href="platforms.html">Platforms</a>', $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-orm/en/2.6/reference/improving-performance.html');
        self::assertStringContainsString('<a href="../tutorials/extra-lazy-associations.html">tutorial</a>', $crawler->html());
    }

    public function testSitemap(): void
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

    public function testAtom(): void
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

    public function testSearchBoxPlaceholder(): void
    {
        $crawler = $this->assertValid('/index.html');

        self::assertStringContainsString("placeholder: 'Search'", $crawler->html());

        $crawler = $this->assertValid('/projects/migrations.html');

        self::assertStringContainsString("placeholder: 'Search Migrations'", $crawler->html());

        $crawler = $this->assertValid('/projects/doctrine-migrations/en/1.7/index.html');

        self::assertStringContainsString("placeholder: 'Search Migrations 1.7'", $crawler->html());
    }

    public function testContribute(): void
    {
        $crawler = $this->assertValid('/contribute/index.html');

        self::assertStringContainsString('<div class="section" id="contribute">', $crawler->html());
        self::assertStringContainsString('<h1>Contribute</h1>', $crawler->html());
    }

    public function testContributeMaintainer(): void
    {
        $crawler = $this->assertValid('/contribute/maintainer/index.html');

        self::assertStringContainsString('<div class="section" id="maintainer-workflow">', $crawler->html());
        self::assertStringContainsString('<h1>Maintainer Workflow</h1>', $crawler->html());
    }

    public function testContributeWebsite(): void
    {
        $crawler = $this->assertValid('/contribute/website/index.html');

        self::assertStringContainsString('<div class="section" id="contribute-to-website">', $crawler->html());
        self::assertStringContainsString('<h1>Contribute to Website</h1>', $crawler->html());
    }

    private function getFullPath(string $path): string
    {
        $fullPath = $this->buildDir . $path;

        if (file_exists($fullPath)) {
            return $this->buildDir . $path;
        }

        return $this->buildDir . $path;
    }

    private function getFileContents(string $path): string
    {
        $html = file_get_contents($path);

        if ($html === false) {
            self::fail(sprintf('File %s contents empty.', $path));
        }

        return $html;
    }

    private function assertValid(string $path): Crawler
    {
        $fullPath = $this->getFullPath($path);

        $html = $this->getFileContents($fullPath);

        $crawler = new Crawler($html, $path, 'http://localhost');

        self::assertCount(1, $crawler->filter('body'), sprintf('%s has a body', $path));

        return $crawler;
    }

    private function getBuildDir(): string|null
    {
        $foldersToCheck = [
            'build-test',
            'build-dev',
        ];

        foreach ($foldersToCheck as $folderToCheck) {
            $path = $this->rootDir . '/' . $folderToCheck;

            if (is_dir($path)) {
                return $path;
            }
        }

        return null;
    }
}
