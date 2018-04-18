<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

class ProjectFactory
{
    public function create(array $project) : Project
    {
        return new Project($project);
    }
}
