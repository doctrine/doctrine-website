<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Repositories\ContributorRepository;

class ContributorRequests
{
    /** @param ContributorRepository<Contributor> $contributorRepository */
    public function __construct(private ContributorRepository $contributorRepository)
    {
    }

    public function getContributors(): RequestCollection
    {
        /** @var Contributor[] $contributors */
        $contributors = $this->contributorRepository->findAll();

        $requests = [];

        foreach ($contributors as $contributor) {
            $requests[] = [
                'github' => $contributor->getGithub(),
            ];
        }

        return new ArrayRequestCollection($requests);
    }
}
