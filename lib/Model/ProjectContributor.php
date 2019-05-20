<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

class ProjectContributor implements LoadMetadataInterface, CommitterStats
{
    /** @var TeamMember|null */
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

    public function getTeamMember() : ?TeamMember
    {
        return $this->teamMember;
    }

    public function isTeamMember() : bool
    {
        return $this->teamMember !== null;
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
