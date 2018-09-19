<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
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

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        $blogPosts       = $this->blogPostRepository->findPaginated(1, 10);
        $primaryProjects = $this->projectRepository->findPrimaryProjects();
        $doctrineUsers   = $this->doctrineUserRepository->findAll();

        return new ControllerResult([
            'blogPosts' => $blogPosts,
            'primaryProjects' => $primaryProjects,
            'doctrineUsers' => $doctrineUsers,
        ]);
    }
}
