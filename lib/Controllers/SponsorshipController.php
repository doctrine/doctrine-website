<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\SponsorRepository;

class SponsorshipController
{
    /** @var SponsorRepository */
    private $sponsorRepository;

    public function __construct(SponsorRepository $sponsorRepository)
    {
        $this->sponsorRepository = $sponsorRepository;
    }

    public function index() : Response
    {
        $sponsors = $this->sponsorRepository->findAllOrderedByHighlighted();

        return new Response(['sponsors' => $sponsors]);
    }
}
