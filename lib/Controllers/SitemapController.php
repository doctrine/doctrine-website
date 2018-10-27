<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\SitemapPageRepository;

class SitemapController
{
    /** @var SitemapPageRepository */
    private $sitemapPageRepository;

    public function __construct(SitemapPageRepository $sitemapPageRepository)
    {
        $this->sitemapPageRepository = $sitemapPageRepository;
    }

    public function index() : Response
    {
        return new Response(['pages' => $this->sitemapPageRepository->findAll()]);
    }
}
