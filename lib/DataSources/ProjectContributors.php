<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\ProjectContributorDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class ProjectContributors implements DataSource
{
    /**
     * @param TeamMemberRepository<TeamMember> $teamMemberRepository
     * @param ProjectRepository<Project>       $projectRepository
     */
    public function __construct(
        private WebsiteDataReader $dataReader,
        private TeamMemberRepository $teamMemberRepository,
        private ProjectRepository $projectRepository,
    ) {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        $projectContributors = $this->dataReader
            ->read(ProjectContributorDataBuilder::DATA_FILE)
            ->getData();

        foreach ($projectContributors as $key => $projectContributor) {
            $projectContributors[$key]['teamMember'] = $this->teamMemberRepository
                ->findOneByGithub($projectContributor['github']);

            $projectContributors[$key]['project'] = $this->projectRepository
                ->findOneBySlug($projectContributor['projectSlug']);
        }

        return $projectContributors;
    }
}
