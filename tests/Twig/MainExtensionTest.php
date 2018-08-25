<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Twig\MainExtension;
use Parsedown;
use PHPUnit\Framework\TestCase;
use function current;
use function sort;

class MainExtensionTest extends TestCase
{
    /** @var Parsedown */
    private $parsedown;

    /** @var MainExtension */
    private $mainExtension;

    protected function setUp() : void
    {
        $this->parsedown = $this->createMock(Parsedown::class);

        $this->mainExtension = new MainExtension(
            $this->parsedown
        );
    }

    public function testGetSearchBoxPlaceholder() : void
    {
        $placeholder = $this->mainExtension->getSearchBoxPlaceholder();

        self::assertSame('Search', $placeholder);

        $project = new Project(['shortName' => 'ORM']);

        $placeholder = $this->mainExtension->getSearchBoxPlaceholder($project);

        self::assertSame('Search ORM', $placeholder);

        $project = new Project([
            'shortName' => 'ORM',
            'versions' => [
                [
                    'slug' => 'latest',
                    'name' => '1.0',
                ],
            ],
        ]);

        $placeholder = $this->mainExtension->getSearchBoxPlaceholder($project, 'latest');

        self::assertSame('Search ORM 1.0', $placeholder);
    }

    public function testGetAssetUrl() : void
    {
        $url = $this->mainExtension->getAssetUrl(
            '/js/main.js',
            'http://lcl.doctrine-project.org'
        );

        self::assertSame('http://lcl.doctrine-project.org/js/main.js?4138a7', $url);
    }

    public function testGetDocsUrls() : void
    {
        $urls = $this->mainExtension->getDocsUrls();

        sort($urls);

        $first = current($urls);

        self::assertSame('/projects/annotations.html', $first['url']);
    }
}
