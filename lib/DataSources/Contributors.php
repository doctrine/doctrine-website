<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\ContributorDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class Contributors implements DataSource
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
        $contributors = $this->dataReader
            ->read(ContributorDataBuilder::DATA_FILE)
            ->getData();

        foreach ($contributors as $key => $contributor) {
            $contributors[$key]['teamMember'] = $this->teamMemberRepository
                ->findOneByGithub($contributor['github']);

            $projects = [];

            foreach ($contributor['projects'] as $slug) {
                $projects[] = $this->projectRepository->findOneBySlug($slug);
            }

            $contributors[$key]['projects'] = $projects;
        }

        return $contributors;
    }
}
