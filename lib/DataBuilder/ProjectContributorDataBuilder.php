<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

use Doctrine\Website\Github\GithubProjectContributors;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class ProjectContributorDataBuilder implements DataBuilder
{
    public const DATA_FILE = 'project_contributors';

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

    public function getName(): string
    {
        return self::DATA_FILE;
    }

    public function build(): WebsiteData
    {
        $projects = $this->projectRepository->findAll();

        $projectContributors = [];

        $this->githubProjectContributors->warmProjectsContributors($projects);

        foreach ($projects as $project) {
            $contributors = $this->githubProjectContributors->getProjectContributors($project);

            foreach ($contributors as $contributor) {
                $numAdditions = 0;
                $numDeletions = 0;

                foreach ($contributor['weeks'] as $week) {
                    $numAdditions += $week['a'];
                    $numDeletions += $week['d'];
                }

                if (! isset($contributor['author']['login'])) {
                    continue;
                }

                $teamMember = $this->teamMemberRepository->findOneByGithub(
                    $contributor['author']['login'],
                );

                $isMaintainer = $teamMember !== null
                    ? $teamMember->isProjectMaintainer($project)
                    : false;

                $isTeamMember = $teamMember !== null;

                $projectContributors[] = [
                    'isTeamMember' => $isTeamMember,
                    'isMaintainer' => $isMaintainer,
                    'projectSlug' => $project->getSlug(),
                    'github' => $contributor['author']['login'],
                    'avatarUrl' => $contributor['author']['avatar_url'],
                    'numCommits' => $contributor['total'],
                    'numAdditions' => $numAdditions,
                    'numDeletions' => $numDeletions,
                ];
            }
        }

        return new WebsiteData(self::DATA_FILE, $projectContributors);
    }
}
