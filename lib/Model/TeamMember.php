<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Closure;
use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\Website\Repositories\ContributorRepository;
use function assert;
use function in_array;

class TeamMember implements HydratableInterface, LoadMetadataInterface, CommitterStats
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

    /**
     * @param mixed[] $teamMember
     */
    public function hydrate(array $teamMember, ObjectManagerInterface $objectManager) : void
    {
        $this->name        = (string) ($teamMember['name'] ?? '');
        $this->github      = (string) ($teamMember['github'] ?? '');
        $this->twitter     = (string) ($teamMember['twitter'] ?? '');
        $this->avatarUrl   = (string) ($teamMember['avatarUrl'] ?? '');
        $this->website     = (string) ($teamMember['website'] ?? '');
        $this->location    = (string) ($teamMember['location'] ?? '');
        $this->maintains   = $teamMember['maintains'] ?? [];
        $this->consultant  = (bool) ($teamMember['consultant'] ?? false);
        $this->headshot    = (string) ($teamMember['headshot'] ?? '');
        $this->bio         = (string) ($teamMember['bio'] ?? '');
        $this->contributor = static function (string $github) use ($objectManager) : Contributor {
            $contributorRepository = $objectManager
                ->getRepository(Contributor::class);

            assert($contributorRepository instanceof ContributorRepository);

            return $contributorRepository->findOneByGithub($github);
        };
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
