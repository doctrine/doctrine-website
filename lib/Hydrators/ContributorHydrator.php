<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;

/**
 * @property TeamMember|null $teamMember
 * @property string $github
 * @property string $avatarUrl
 * @property int $numCommits
 * @property int $numAdditions
 * @property int $numDeletions
 * @property Project[] $projects
 * @template-extends ModelHydrator<Contributor>
 */
final class ContributorHydrator extends ModelHydrator
{
    /** @return class-string<Contributor> */
    protected function getClassName(): string
    {
        return Contributor::class;
    }

    /** @param mixed[] $data */
    protected function doHydrate(array $data): void
    {
        $this->teamMember   = $data['teamMember'] ?? null;
        $this->github       = (string) ($data['github'] ?? '');
        $this->avatarUrl    = (string) ($data['avatarUrl'] ?? '');
        $this->numCommits   = (int) ($data['numCommits'] ?? 0);
        $this->numAdditions = (int) ($data['numAdditions'] ?? 0);
        $this->numDeletions = (int) ($data['numDeletions'] ?? 0);
        $this->projects     = $data['projects'] ?? [];
    }
}
