<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\SitemapPages;
use Doctrine\Website\Tests\TestCase;
use function date;

class SitemapPagesTest extends TestCase
{
    /** @var SitemapPages */
    private $sitemapPages;

    public function testFindAll() : void
    {
        $sitemapPages = $this->sitemapPages->getData();

        self::assertCount(5, $sitemapPages);

        self::assertSame(date('Y-m-d'), $sitemapPages[0]['date']->format('Y-m-d'));

        self::assertSame('/', $sitemapPages[0]['url']);
        self::assertSame('/projects/doctrine-inflector.html', $sitemapPages[1]['url']);
        self::assertSame('/projects/doctrine-orm.html', $sitemapPages[2]['url']);
        self::assertSame('/api/inflector.html', $sitemapPages[3]['url']);
        self::assertSame('/api/orm.html', $sitemapPages[4]['url']);
    }

    protected function setUp() : void
    {
        $this->sitemapPages = new SitemapPages(__DIR__ . '/../source');
    }
}
