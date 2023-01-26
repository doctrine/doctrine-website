<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\SitemapPageRepository;

class SitemapController
{
    public function __construct(private SitemapPageRepository $sitemapPageRepository)
    {
    }

    public function index(): Response
    {
        return new Response(['pages' => $this->sitemapPageRepository->findAll()]);
    }
}
