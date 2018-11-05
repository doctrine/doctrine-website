<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\TeamMemberRepository;

class TestGithubProjectContributors implements GithubProjectContributors
{
    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }

    /**
     * @param Project[] $projects
     */
    public function warmProjectsContributors(array $projects) : void
    {
    }

    public function warmProjectContributors(Project $project) : void
    {
    }

    public function waitForProjectContributorsData(Project $project) : void
    {
    }

    /**
     * @return mixed[]
     */
    public function getProjectContributors(Project $project) : array
    {
        $projectContributors = [];

        /** @var TeamMember[] $teamMembers */
        $teamMembers = $this->teamMemberRepository->findAll();

        foreach ($teamMembers as $teamMember) {
            $projectContributors[] = [
                'weeks' => [
                    ['a' => 1, 'd' => 1],
                    ['a' => 1, 'd' => 1],
                ],
                'total' => 2,
                'author' => [
                    'login' =>  $teamMember->getGithub(),
                    'avatar_url' => $teamMember->getAvatarUrl(),
                ],
            ];
        }

        $contributors = [
            [
                'login' => 'fabpot',
                'avatar_url' => 'https://avatars3.githubusercontent.com/u/47313?v=4',
            ],
            [
                'login' => 'Seldaek',
                'avatar_url' => 'https://avatars1.githubusercontent.com/u/183678?v=4',
            ],
            [
                'login' => 'kriswallsmith',
                'avatar_url' => 'https://avatars2.githubusercontent.com/u/33886?v=4',
            ],
        ];

        foreach ($contributors as $contributor) {
            $projectContributors[] = [
                'weeks' => [
                    ['a' => 1, 'd' => 1],
                    ['a' => 1, 'd' => 1],
                ],
                'total' => 2,
                'author' => [
                    'login' =>  $contributor['login'],
                    'avatar_url' => $contributor['avatar_url'],
                ],
            ];
        }

        return $projectContributors;
    }
}
