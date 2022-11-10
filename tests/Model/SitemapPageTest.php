<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Model;

use DateTimeImmutable;
use Doctrine\Website\Model\SitemapPage;
use Doctrine\Website\Tests\TestCase;

class SitemapPageTest extends TestCase
{
    /** @var string */
    private $url;

    /** @var DateTimeImmutable */
    private $date;

    /** @var SitemapPage */
    private $sitemapPage;

    public function testGetUrl(): void
    {
        self::assertSame($this->url, $this->sitemapPage->getUrl());
    }

    public function testGetDate(): void
    {
        self::assertSame($this->date, $this->sitemapPage->getDate());
    }

    protected function setUp(): void
    {
        $this->url  = '/test.html';
        $this->date = new DateTimeImmutable();

        $this->sitemapPage = new SitemapPage(
            $this->url,
            $this->date,
        );
    }
}
