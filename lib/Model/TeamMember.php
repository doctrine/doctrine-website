<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Closure;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use function in_array;

class TeamMember implements LoadMetadataInterface, CommitterStats
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

    /** @var string[] */
    private $maintains = [];

    /** @var bool */
    private $consultant = false;

    /** @var string */
    private $headshot;

    /** @var string */
    private $bio;

    /** @var Closure|Contributor */
    private $contributor;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['github']);
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

    public function isProjectMaintainer(Project $project) : bool
    {
        return in_array($project->getSlug(), $this->maintains, true);
    }

    public function isConsultant() : bool
    {
        return $this->consultant;
    }

    public function getHeadshot() : string
    {
        return $this->headshot;
    }

    public function getBio() : string
    {
        return $this->bio;
    }

    public function getContributor() : Contributor
    {
        if ($this->contributor instanceof Closure) {
            $this->contributor = ($this->contributor)($this->github);
        }

        return $this->contributor;
    }

    public function getNumCommits() : int
    {
        return $this->getContributor()->getNumCommits();
    }

    public function getNumAdditions() : int
    {
        return $this->getContributor()->getNumAdditions();
    }

    public function getNumDeletions() : int
    {
        return $this->getContributor()->getNumDeletions();
    }
}
