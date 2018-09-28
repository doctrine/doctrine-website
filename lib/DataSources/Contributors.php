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
    public function getSourceRows() : array
    {
        $projectContributors = $this->projectContributorRepository->findAll();

        $contributorRows = [];

        foreach ($projectContributors as $projectContributor) {
            $github = $projectContributor->getGithub();

            if (! isset($contributorRows[$github])) {
                $contributorRows[$github] = [
                    'teamMember' => $projectContributor->getTeamMember(),
                    'isTeamMember' => $projectContributor->isTeamMember(),
                    'github' => $github,
                    'avatarUrl' => $projectContributor->getAvatarUrl(),
                    'numCommits' => 0,
                    'numAdditions' => 0,
                    'numDeletions' => 0,
                    'projects' => [],
                ];
            }

            $contributorRows[$github]['numCommits']   += $projectContributor->getNumCommits();
            $contributorRows[$github]['numAdditions'] += $projectContributor->getNumAdditions();
            $contributorRows[$github]['numDeletions'] += $projectContributor->getNumDeletions();
            $contributorRows[$github]['projects'][]    = $projectContributor->getProject();
        }

        return $contributorRows;
    }
}
