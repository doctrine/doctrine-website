<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Model\BlogPost;
use Doctrine\Website\Repositories\BlogPostRepository;

final readonly class BlogController
{
    /** @param BlogPostRepository<BlogPost> $blogPostRepository */
    public function __construct(
        private BlogPostRepository $blogPostRepository,
    ) {
    }

    public function index(): Response
    {
        return new Response([
            'blogPosts' => $this->blogPostRepository->findPaginated(),
        ]);
    }

    public function archive(): Response
    {
        return new Response([
            'blogPosts' => $this->blogPostRepository->findAll(),
        ]);
    }

    public function view(string $slug): Response
    {
        return new Response([
            'blogPost' => $this->blogPostRepository->find($slug),
        ]);
    }
}
