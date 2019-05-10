<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use function array_merge;

final class Partner implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var string */
    private $url;

    /** @var UtmParameters */
    private $utmParameters;

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
        $this->name          = (string) ($partner['name'] ?? '');
        $this->slug          = (string) ($partner['slug'] ?? '');
        $this->url           = (string) ($partner['url'] ?? '');
        $this->utmParameters = new UtmParameters(
            array_merge(
                [
                    'utm_source'  => 'doctrine',
                    'utm_medium'   => 'website',
                    'utm_campaign' => 'partners',
                ],
                $partner['utmParameters'] ?? []
            )
        );
        $this->logo          = (string) ($partner['logo'] ?? '');
        $this->bio           = (string) ($partner['bio'] ?? '');
        $this->details       = new PartnerDetails(
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

    /**
     * @param string[] $parameters
     */
    public function getUrlWithUtmParameters(array $parameters = []) : string
    {
        return $this->utmParameters->buildUrl($this->url, $parameters);
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
