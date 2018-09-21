<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;
use Github\Api\Repo;
use Github\Client;
use Github\Exception\RuntimeException;

class ProjectContributors implements DataSource
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    /** @var Client */
    private $githubClient;

    public function __construct(
        ProjectRepository $projectRepository,
        TeamMemberRepository $teamMemberRepository,
        Client $githubClient
    ) {
        $this->projectRepository    = $projectRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->githubClient         = $githubClient;
    }

    /**
     * @return mixed[][]
     */
    public function getData() : array
    {
        $projects = $this->projectRepository->findAll();

        $data = [];

        foreach ($projects as $project) {
            $contributors = $this->getProjectContributors($project);

            foreach ($contributors as $contributor) {
                $numAdditions = 0;
                $numDeletions = 0;

                foreach ($contributor['weeks'] as $week) {
                    $numAdditions += $week['a'];
                    $numDeletions += $week['d'];
                }

                $teamMember = $this->teamMemberRepository->findOneByGithub($contributor['author']['login']);

                $data[] = [
                    'teamMember' => $teamMember,
                    'isTeamMember' => $teamMember !== null,
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

        return $data;
    }

    /**
     * @return mixed[]
     */
    private function getProjectContributors(Project $project) : array
    {
        try {
            /** @var Repo $repo */
            $repo = $this->githubClient->api('repo');

            return $repo->statistics('doctrine', $project->getRepositoryName());
        } catch (RuntimeException $e) {
            return [];
        }
    }
}
