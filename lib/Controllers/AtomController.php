<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Blog\BlogPostRepository;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use function array_slice;

class AtomController
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
            'blogPosts' => array_slice($this->blogPostRepository->findAll(), 0, 10),
        ]);
    }
}
