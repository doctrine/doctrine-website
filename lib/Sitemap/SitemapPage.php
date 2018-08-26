<?php

declare(strict_types=1);

namespace Doctrine\Website\Sitemap;

use DateTimeImmutable;

class SitemapPage
{
    /** @var string */
    private $url;

    /** @var DateTimeImmutable */
    private $date;

    public function __construct(string $url, DateTimeImmutable $date)
    {
        $this->url  = $url;
        $this->date = $date;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getDate() : DateTimeImmutable
    {
        return $this->date;
    }
}
