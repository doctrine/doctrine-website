<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class TeamMember implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $github;

    /** @var string */
    private $twitter;

    /** @var string */
    private $avatarUrl;

    /** @var string */
    private $website;

    /** @var string */
    private $location;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['github']);
    }

    /**
     * @param mixed[] $teamMember
     */
    public function hydrate(array $teamMember, ObjectManagerInterface $objectManager) : void
    {
        $this->name      = $teamMember['name'] ?? '';
        $this->github    = $teamMember['github'] ?? '';
        $this->twitter   = $teamMember['twitter'] ?? '';
        $this->avatarUrl = $teamMember['avatarUrl'] ?? '';
        $this->website   = $teamMember['website'] ?? '';
        $this->location  = $teamMember['location'] ?? '';
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getGithub() : string
    {
        return $this->github;
    }

    public function getTwitter() : string
    {
        return $this->twitter;
    }

    public function getAvatarUrl() : string
    {
        return $this->avatarUrl;
    }

    public function getWebsite() : string
    {
        return $this->website;
    }

    public function getLocation() : string
    {
        return $this->location;
    }
}
