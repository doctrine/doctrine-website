<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\ProjectContributorDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class ProjectContributors implements DataSource
{
    /** @var WebsiteDataReader */
    private $dataReader;

    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(
        WebsiteDataReader $dataReader,
        TeamMemberRepository $teamMemberRepository,
        ProjectRepository $projectRepository
    ) {
        $this->dataReader           = $dataReader;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->projectRepository    = $projectRepository;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
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
