<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Repositories\SitemapPageRepository;

class SitemapController
{
    /** @var SitemapPageRepository */
    private $sitemapPageRepository;

    public function __construct(SitemapPageRepository $sitemapPageRepository)
    {
        $this->sitemapPageRepository = $sitemapPageRepository;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult(['pages' => $this->sitemapPageRepository->findAll()]);
    }
}
