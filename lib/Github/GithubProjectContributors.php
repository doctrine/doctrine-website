<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Doctrine\Website\Model\Project;

interface GithubProjectContributors
{
    /**
     * @return mixed[]
     */
    public function getProjectContributors(Project $project) : array;
}
