<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Doctrine\Website\Model\Project;

interface GithubProjectContributors
{
    /** @param Project[] $projects */
    public function warmProjectsContributors(array $projects): void;

    /** @return mixed[] */
    public function getProjectContributors(Project $project): array;
}
