<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class DoctrineUser implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $url;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['name']);
    }

    /**
     * @param mixed[] $doctrineUser
     */
    public function hydrate(array $doctrineUser, ObjectManagerInterface $objectManager) : void
    {
        $this->name = (string) $doctrineUser['name'];
        $this->url  = (string) $doctrineUser['url'];
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUrl() : string
    {
        return $this->url;
    }
}
