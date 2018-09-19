<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;
use Doctrine\Website\Github\GithubProjectContributors;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class ProjectContributors implements DataSource
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    /** @var GithubProjectContributors */
    private $githubProjectContributors;

    public function __construct(
        ProjectRepository $projectRepository,
        TeamMemberRepository $teamMemberRepository,
        GithubProjectContributors $githubProjectContributors
    ) {
        $this->projectRepository         = $projectRepository;
        $this->teamMemberRepository      = $teamMemberRepository;
        $this->githubProjectContributors = $githubProjectContributors;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        $projects = $this->projectRepository->findAll();

        $projectContributorRows = [];

        foreach ($projects as $project) {
            $contributors = $this->githubProjectContributors->getProjectContributors($project);

            foreach ($contributors as $contributor) {
                $numAdditions = 0;
                $numDeletions = 0;

                foreach ($contributor['weeks'] as $week) {
                    $numAdditions += $week['a'];
                    $numDeletions += $week['d'];
                }

                $teamMember = $this->teamMemberRepository->findOneByGithub($contributor['author']['login']);

                $isMaintainer = $teamMember !== null
                    ? $teamMember->isProjectMaintainer($project)
                    : false;

                $isTeamMember = $teamMember !== null;

                $projectContributorRows[] = [
                    'teamMember' => $teamMember,
                    'isTeamMember' => $isTeamMember,
                    'isMaintainer' => $isMaintainer,
                    'projectSlug' => $project->getSlug(),
                    'project' => $project,
                    'github' => $contributor['author']['login'],
                    'avatarUrl' => $contributor['author']['avatar_url'],
                    'numCommits' => $contributor['total'],
                    'numAdditions' => $numAdditions,
                    'numDeletions' => $numDeletions,
                ];
            }
        }

        return $projectContributorRows;
    }
}
