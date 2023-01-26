<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

final class Partner implements LoadMetadataInterface
{
    private string $name;

    private string $slug;

    private string $url;

    private UtmParameters $utmParameters;

    private string $logo;

    private string $bio;

    private PartnerDetails $details;

    private bool $featured;

    public static function loadMetadata(ClassMetadataInterface $metadata): void
    {
        $metadata->setIdentifier(['slug']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /** @param string[] $parameters */
    public function getUrlWithUtmParameters(array $parameters = []): string
    {
        return $this->utmParameters->buildUrl($this->url, $parameters);
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getDetails(): PartnerDetails
    {
        return $this->details;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }
}
