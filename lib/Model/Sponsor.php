<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

final class Sponsor implements LoadMetadataInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var UtmParameters */
    private $utmParameters;

    /** @var bool */
    private $highlighted;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['name']);
    }

    public function getName() : string
    {
        return $this->name;
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

    public function isHighlighted() : bool
    {
        return $this->highlighted;
    }
}
