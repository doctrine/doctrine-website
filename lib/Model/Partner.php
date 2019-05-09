<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

final class Partner implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var string */
    private $url;

    /** @var string */
    private $logo;

    /** @var string */
    private $bio;

    /** @var PartnerDetails */
    private $details;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['slug']);
    }

    /**
     * @param mixed[] $partner
     */
    public function hydrate(array $partner, ObjectManagerInterface $objectManager) : void
    {
        $this->name    = (string) ($partner['name'] ?? '');
        $this->slug    = (string) ($partner['slug'] ?? '');
        $this->url     = (string) ($partner['url'] ?? '');
        $this->logo    = (string) ($partner['logo'] ?? '');
        $this->bio     = (string) ($partner['bio'] ?? '');
        $this->details = new PartnerDetails(
            (string) ($partner['details']['label'] ?? ''),
            $partner['details']['items'] ?? []
        );
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getLogo() : string
    {
        return $this->logo;
    }

    public function getBio() : string
    {
        return $this->bio;
    }

    public function getDetails() : PartnerDetails
    {
        return $this->details;
    }
}
