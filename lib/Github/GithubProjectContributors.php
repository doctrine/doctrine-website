<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Doctrine\Website\Model\Project;

interface GithubProjectContributors
{
    /**
     * @param Project[] $projects
     */
    public function warmProjectsContributors(array $projects) : void;

    public function warmProjectContributors(Project $project) : void;

    public function waitForProjectContributorsData(Project $project) : void;

    /**
     * @return mixed[]
     */
    public function getProjectContributors(Project $project) : array;
}
