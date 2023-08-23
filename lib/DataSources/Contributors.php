<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\ContributorDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class Contributors implements DataSource
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
