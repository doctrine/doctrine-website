<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\BlogPostRepository;
use Doctrine\Website\Repositories\DoctrineUserRepository;
use Doctrine\Website\Repositories\ProjectRepository;

class HomepageController
{
    /** @var BlogPostRepository */
    private $blogPostRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var DoctrineUserRepository */
    private $doctrineUserRepository;

    public function __construct(
        BlogPostRepository $blogPostRepository,
        ProjectRepository $projectRepository,
        DoctrineUserRepository $doctrineUserRepository
    ) {
        $this->blogPostRepository     = $blogPostRepository;
        $this->projectRepository      = $projectRepository;
        $this->doctrineUserRepository = $doctrineUserRepository;
    }

    public function index() : Response
    {
        $blogPosts       = $this->blogPostRepository->findPaginated(1, 10);
        $primaryProjects = $this->projectRepository->findPrimaryProjects();
        $doctrineUsers   = $this->doctrineUserRepository->findAll();

        return new Response([
            'blogPosts' => $blogPosts,
            'primaryProjects' => $primaryProjects,
            'doctrineUsers' => $doctrineUsers,
        ]);
    }
}
