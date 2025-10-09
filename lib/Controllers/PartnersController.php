<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Model\Partner;
use Doctrine\Website\Repositories\PartnerRepository;
use Doctrine\Website\StaticGenerator\Controller\Response;

final readonly class PartnersController
{
    /** @param PartnerRepository<Partner> $partnerRepository */
    public function __construct(
        private PartnerRepository $partnerRepository,
    ) {
    }

    public function index(): Response
    {
        $partners = $this->partnerRepository->findAll();

        return new Response(['partners' => $partners]);
    }

    public function view(string $slug): Response
    {
        $partner = $this->partnerRepository->findOneBySlug($slug);

        return new Response(['partner' => $partner], '/partner.html.twig');
    }
}
