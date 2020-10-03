<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectContributor;

/**
 * @property TeamMember|null $teamMember
 * @property string $projectSlug
 * @property Project $project
 * @property string $github
 * @property string $avatarUrl
 * @property int $numCommits
 * @property int $numAdditions
 * @property int $numDeletions
 */
final class ProjectContributorHydrator extends ModelHydrator
{
    protected function getClassName(): string
    {
        return ProjectContributor::class;
    }

    /**
     * @param mixed[] $data
     */
    protected function doHydrate(array $data): void
    {
        $this->teamMember   = $data['teamMember'] ?? null;
        $this->projectSlug  = (string) ($data['projectSlug'] ?? '');
        $this->project      = $data['project'] ?? new Project();
        $this->github       = (string) ($data['github'] ?? '');
        $this->avatarUrl    = (string) ($data['avatarUrl'] ?? '');
        $this->numCommits   = (int) ($data['numCommits'] ?? 0);
        $this->numAdditions = (int) ($data['numAdditions'] ?? 0);
        $this->numDeletions = (int) ($data['numDeletions'] ?? 0);
    }
}
