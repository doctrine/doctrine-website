<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class Contributor implements HydratableInterface, LoadMetadataInterface
{
    /** @var null|TeamMember */
    private $teamMember;

    /** @var string */
    private $github;

    /** @var string */
    private $avatarUrl;

    /** @var int */
    private $numCommits;

    /** @var int */
    private $numAdditions;

    /** @var int */
    private $numDeletions;

    /** @var Project[] */
    private $projects;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['github']);
    }

    /**
     * @param mixed[] $contributor
     */
    public function hydrate(array $contributor, ObjectManagerInterface $objectManager) : void
    {
        $this->teamMember   = $contributor['teamMember'];
        $this->github       = $contributor['github'];
        $this->avatarUrl    = $contributor['avatarUrl'];
        $this->numCommits   = $contributor['numCommits'];
        $this->numAdditions = $contributor['numAdditions'];
        $this->numDeletions = $contributor['numDeletions'];
        $this->projects     = $contributor['projects'];
    }

    public function getTeamMember() : ?TeamMember
    {
        return $this->teamMember;
    }

    public function getGithub() : string
    {
        return $this->github;
    }

    public function getAvatarUrl() : string
    {
        return $this->avatarUrl;
    }

    public function getNumCommits() : int
    {
        return $this->numCommits;
    }

    public function getNumAdditions() : int
    {
        return $this->numAdditions;
    }

    public function getNumDeletions() : int
    {
        return $this->numDeletions;
    }

    /**
     * @return Project[]
     */
    public function getProjects() : array
    {
        return $this->projects;
    }
}
