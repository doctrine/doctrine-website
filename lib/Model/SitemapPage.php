<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class SitemapPage implements HydratableInterface, LoadMetadataInterface
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

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['url']);
    }

    /**
     * @param mixed[] $sitemapPage
     */
    public function hydrate(array $sitemapPage, ObjectManagerInterface $objectManager) : void
    {
        $this->url  = (string) ($sitemapPage['url'] ?? '');
        $this->date = $sitemapPage['date'] ?? new DateTimeImmutable();
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
