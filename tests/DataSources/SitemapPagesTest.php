<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSource\Sorter;
use Doctrine\Website\DataSources\SitemapPages;
use Doctrine\Website\Tests\TestCase;
use function date;
use function usort;

class SitemapPagesTest extends TestCase
{
    /** @var SitemapPages */
    private $sitemapPages;

    public function testGetSourceRows() : void
    {
        $sitemapPageRows = $this->sitemapPages->getSourceRows();

        usort($sitemapPageRows, new Sorter(['url' => 'asc']));

        self::assertCount(5, $sitemapPageRows);

        self::assertSame(date('Y-m-d'), $sitemapPageRows[0]['date']->format('Y-m-d'));

        self::assertSame('/', $sitemapPageRows[0]['url']);
        self::assertSame('/api/inflector.html', $sitemapPageRows[1]['url']);
        self::assertSame('/api/orm.html', $sitemapPageRows[2]['url']);
        self::assertSame('/projects/doctrine-inflector.html', $sitemapPageRows[3]['url']);
        self::assertSame('/projects/doctrine-orm.html', $sitemapPageRows[4]['url']);
    }

    protected function setUp() : void
    {
        $this->sitemapPages = new SitemapPages(__DIR__ . '/../source');
    }
}
