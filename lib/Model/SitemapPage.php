<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

class SitemapPage implements LoadMetadataInterface
{
    public function __construct(private string $url, private DateTimeImmutable $date)
    {
    }

    public static function loadMetadata(ClassMetadataInterface $metadata): void
    {
        $metadata->setIdentifier(['url']);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
