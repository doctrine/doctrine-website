<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Closure;
use Doctrine\Website\Projects\Project;
use Twig_Extension;
use Twig_SimpleFunction;
use function array_filter;
use function in_array;
use function ksort;

class TeamExtension extends Twig_Extension
{
    /** @var mixed[] */
    private $teamMembers;

    /**
     * @param mixed[] $teamMembers
     */
    public function __construct(array $teamMembers)
    {
        $this->teamMembers = $teamMembers;
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('get_active_core_team_members', [$this, 'getActiveCoreTeamMembers']),
            new Twig_SimpleFunction('get_active_documentation_team_members', [$this, 'getActiveDocumentationTeamMembers']),
            new Twig_SimpleFunction('get_inactive_team_members', [$this, 'getInactiveTeamMembers']),
            new Twig_SimpleFunction('get_active_project_team_members', [$this, 'getActiveProjectTeamMembers']),
            new Twig_SimpleFunction('get_inactive_project_team_members', [$this, 'getInactiveProjectTeamMembers']),
            new Twig_SimpleFunction('get_all_project_team_members', [$this, 'getAllProjectTeamMembers']),
        ];
    }

    /**
     * @return mixed[]
     */
    public function getTeamMembers(?Closure $filter = null) : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $name = $teamMember['name'] ?? $key;

            $teamMembers[$name] = $teamMember;
        }

        if ($filter !== null) {
            $teamMembers = array_filter($teamMembers, $filter);
        }

        ksort($teamMembers);

        return $teamMembers;
    }

    /**
     * @return mixed[]
     */
    public function getActiveCoreTeamMembers() : array
    {
        return $this->getTeamMembers(function (array $teamMember) {
            $active = $teamMember['active'] ?? false;
            $core   = $teamMember['core'] ?? false;

            return $active && $core;
        });
    }

    /**
     * @return mixed[]
     */
    public function getActiveDocumentationTeamMembers() : array
    {
        return $this->getTeamMembers(function (array $teamMember) {
            $active        = $teamMember['active'] ?? false;
            $documentation = $teamMember['documentation'] ?? false;

            return $active && $documentation;
        });
    }

    /**
     * @return mixed[]
     */
    public function getInactiveTeamMembers() : array
    {
        return $this->getTeamMembers(function (array $teamMember) {
            $active = $teamMember['active'] ?? false;

            return $active === false;
        });
    }

    /**
     * @return mixed[]
     */
    public function getAllProjectTeamMembers(Project $project) : array
    {
        return $this->getTeamMembers(function (array $teamMember) use ($project) {
            return in_array($project->getSlug(), $teamMember['projects'] ?? [], true);
        });
    }

    /**
     * @return mixed[]
     */
    public function getActiveProjectTeamMembers(Project $project) : array
    {
        return $this->getTeamMembers(function (array $teamMember) use ($project) {
            $active = $teamMember['active'] ?? false;

            return $active && in_array($project->getSlug(), $teamMember['projects'] ?? [], true);
        });
    }

    /**
     * @return mixed[]
     */
    public function getInactiveProjectTeamMembers(Project $project) : array
    {
        return $this->getTeamMembers(function (array $teamMember) use ($project) {
            $active = $teamMember['active'] ?? false;

            return ! $active && in_array($project->getSlug(), $teamMember['projects'] ?? [], true);
        });
    }
}
