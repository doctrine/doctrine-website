<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class ProjectContributor implements HydratableInterface, LoadMetadataInterface
{
    /** @var null|TeamMember */
    private $teamMember;

    /** @var string */
    private $projectSlug;

    /** @var Project */
    private $project;

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

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['projectSlug', 'github']);
    }

    /**
     * @param mixed[] $projectContributor
     */
    public function hydrate(array $projectContributor, ObjectManagerInterface $objectManager) : void
    {
        $this->teamMember   = $projectContributor['teamMember'];
        $this->projectSlug  = $projectContributor['projectSlug'];
        $this->project      = $projectContributor['project'];
        $this->github       = $projectContributor['github'];
        $this->avatarUrl    = $projectContributor['avatarUrl'];
        $this->numCommits   = $projectContributor['numCommits'];
        $this->numAdditions = $projectContributor['numAdditions'];
        $this->numDeletions = $projectContributor['numDeletions'];
    }

    public function getTeamMember() : ?TeamMember
    {
        return $this->teamMember;
    }

    public function getProjectSlug() : string
    {
        return $this->projectSlug;
    }

    public function getProject() : Project
    {
        return $this->project;
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
}
