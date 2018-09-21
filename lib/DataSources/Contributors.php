<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;
use Doctrine\Website\Repositories\ProjectContributorRepository;

class Contributors implements DataSource
{
    /** @var ProjectContributorRepository */
    private $projectContributorRepository;

    public function __construct(ProjectContributorRepository $projectContributorRepository)
    {
        $this->projectContributorRepository = $projectContributorRepository;
    }

    /**
     * @return mixed[][]
     */
    public function getData() : array
    {
        $projectContributors = $this->projectContributorRepository->findAll();

        $contributors = [];

        foreach ($projectContributors as $projectContributor) {
            $github = $projectContributor->getGithub();

            if (! isset($contributors[$github])) {
                $contributors[$github] = [
                    'teamMember' => $projectContributor->getTeamMember(),
                    'isTeamMember' => $projectContributor->getTeamMember() !== null,
                    'github' => $github,
                    'avatarUrl' => $projectContributor->getAvatarUrl(),
                    'numCommits' => 0,
                    'numAdditions' => 0,
                    'numDeletions' => 0,
                    'projects' => [],
                ];
            }

            $contributors[$github]['numCommits']   += $projectContributor->getNumCommits();
            $contributors[$github]['numAdditions'] += $projectContributor->getNumAdditions();
            $contributors[$github]['numDeletions'] += $projectContributor->getNumDeletions();
            $contributors[$github]['projects'][]    = $projectContributor->getProject();
        }

        return $contributors;
    }
}
