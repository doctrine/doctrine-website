<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\PartnerRepository;

final class PartnersController
{
    /** @var PartnerRepository */
    private $partnerRepository;

    public function __construct(PartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function index() : Response
    {
        $partners = $this->partnerRepository->findAll();

        return new Response(['partners' => $partners]);
    }

    public function view(string $slug) : Response
    {
        $partner = $this->partnerRepository->findOneBySlug($slug);

        return new Response(['partner' => $partner], '/partner.html.twig');
    }
}
