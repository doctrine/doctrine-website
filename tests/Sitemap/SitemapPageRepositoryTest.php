<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Sitemap;

use Doctrine\Website\Sitemap\SitemapPageRepository;
use Doctrine\Website\Tests\TestCase;
use function date;

class SitemapPageRepositoryTest extends TestCase
{
    /** @var SitemapPageRepository */
    private $sitemapPageRepository;

    public function testFindAll() : void
    {
        $sitemapPages = $this->sitemapPageRepository->findAll();

        self::assertCount(5, $sitemapPages);

        self::assertSame(date('Y-m-d'), $sitemapPages[0]->getDate()->format('Y-m-d'));

        self::assertSame('/', $sitemapPages[0]->getUrl());
        self::assertSame('/projects/doctrine-inflector.html', $sitemapPages[1]->getUrl());
        self::assertSame('/projects/doctrine-orm.html', $sitemapPages[2]->getUrl());
        self::assertSame('/api/inflector.html', $sitemapPages[3]->getUrl());
        self::assertSame('/api/orm.html', $sitemapPages[4]->getUrl());
    }

    protected function setUp() : void
    {
        $this->sitemapPageRepository = new SitemapPageRepository(__DIR__ . '/../source');
    }
}
