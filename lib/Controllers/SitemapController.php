<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Model\SitemapPage;
use Doctrine\Website\Repositories\SitemapPageRepository;

final readonly class SitemapController
{
    /** @param SitemapPageRepository<SitemapPage> $sitemapPageRepository */
    public function __construct(
        private SitemapPageRepository $sitemapPageRepository,
    ) {
    }

    public function index(): Response
    {
        return new Response(['pages' => $this->sitemapPageRepository->findAll()]);
    }
}
