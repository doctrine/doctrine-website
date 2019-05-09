<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

final class Sponsor implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var bool */
    private $highlighted;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['name']);
    }

    /**
     * @param mixed[] $sponsor
     */
    public function hydrate(array $sponsor, ObjectManagerInterface $objectManager) : void
    {
        $this->name        = (string) ($sponsor['name'] ?? '');
        $this->url         = (string) ($sponsor['url'] ?? '');
        $this->highlighted = (bool) ($sponsor['highlighted'] ?? '');
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function isHighlighted() : bool
    {
        return $this->highlighted;
    }
}
