<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\ContributorRepository;

class TeamController
{
    /** @var ContributorRepository */
    private $contributorRepository;

    public function __construct(ContributorRepository $contributorRepository)
    {
        $this->contributorRepository = $contributorRepository;
    }

    public function maintainers(): Response
    {
        return new Response([
            'contributors' => $this->contributorRepository->findMaintainers(),
        ]);
    }

    public function contributors(): Response
    {
        return new Response([
            'contributors' => $this->contributorRepository->findContributors(),
        ]);
    }

    public function contributor(string $github): Response
    {
        $contributor = $this->contributorRepository->findOneByGithub($github);

        return new Response(
            ['contributor' => $contributor],
            '/team/member.html.twig'
        );
    }
}
