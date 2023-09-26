<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

use Doctrine\Website\Github\GithubProjectContributors;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

final readonly class ProjectContributorDataBuilder implements DataBuilder
{
    public const DATA_FILE = 'project_contributors';

    /**
     * @param ProjectRepository<Project>       $projectRepository
     * @param TeamMemberRepository<TeamMember> $teamMemberRepository
     */
    public function __construct(
        private ProjectRepository $projectRepository,
        private TeamMemberRepository $teamMemberRepository,
        private GithubProjectContributors $githubProjectContributors,
    ) {
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

                $isMaintainer = $teamMember?->isProjectMaintainer($project) ?? false;

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
