<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use DateTimeImmutable;
use Doctrine\Website\Hydrators\SitemapPageHydrator;
use Doctrine\Website\Model\SitemapPage;

class SitemapPageHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(SitemapPageHydrator::class);
        $dateTime       = new DateTimeImmutable('1979-01-25');
        $propertyValues = [
            'date' => $dateTime,
            'url' => 'url',
        ];

        $expected = new SitemapPage('url', $dateTime);
        $this->populate($expected, $propertyValues);

        $sitemapPage = new SitemapPage('', new DateTimeImmutable());

        $hydrator->hydrate($sitemapPage, $propertyValues);

        self::assertEquals($expected, $sitemapPage);
    }
}
