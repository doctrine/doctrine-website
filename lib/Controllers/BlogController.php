<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Repositories\BlogPostRepository;

class BlogController
{
    /** @var BlogPostRepository */
    private $blogPostRepository;

    public function __construct(BlogPostRepository $blogPostRepository)
    {
        $this->blogPostRepository = $blogPostRepository;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'blogPosts' => $this->blogPostRepository->findPaginated(),
        ]);
    }

    public function archive(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'blogPosts' => $this->blogPostRepository->findAll(),
        ]);
    }

    public function view(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'blogPost' => $this->blogPostRepository->find($sourceFile->getParameter('url')),
        ]);
    }
}
