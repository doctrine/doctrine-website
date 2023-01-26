<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

use Doctrine\Website\Repositories\ProjectContributorRepository;

class ContributorDataBuilder implements DataBuilder
{
    public const DATA_FILE = 'contributors';

    public function __construct(private ProjectContributorRepository $projectContributorRepository)
    {
    }

    public function getName(): string
    {
        return self::DATA_FILE;
    }

    public function build(): WebsiteData
    {
        $projectContributors = $this->projectContributorRepository->findAll();

        $contributors = [];

        foreach ($projectContributors as $projectContributor) {
            $github = $projectContributor->getGithub();

            if (! isset($contributors[$github])) {
                $contributors[$github] = [
                    'isTeamMember' => $projectContributor->isTeamMember(),
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
            $contributors[$github]['projects'][]    = $projectContributor->getProject()->getSlug();
        }

        return new WebsiteData(self::DATA_FILE, $contributors);
    }
}
