<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

class Contributor implements LoadMetadataInterface, CommitterStats
{
    private TeamMember|null $teamMember = null;

    private string $github;

    private string $avatarUrl;

    private int $numCommits;

    private int $numAdditions;

    private int $numDeletions;

    /** @var Project[] */
    private array $projects;

    public static function loadMetadata(ClassMetadataInterface $metadata): void
    {
        $metadata->setIdentifier(['github']);
    }

    public function getTeamMember(): TeamMember|null
    {
        return $this->teamMember;
    }

    public function getGithub(): string
    {
        return $this->github;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function getNumCommits(): int
    {
        return $this->numCommits;
    }

    public function getNumAdditions(): int
    {
        return $this->numAdditions;
    }

    public function getNumDeletions(): int
    {
        return $this->numDeletions;
    }

    /** @return Project[] */
    public function getProjects(): array
    {
        return $this->projects;
    }
}
