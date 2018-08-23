<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

class ProjectFactory
{
    /**
     * @param mixed[] $project
     */
    public function create(array $project) : Project
    {
        return new Project($project);
    }
}
