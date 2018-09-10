<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Blog\BlogPostRepository;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Projects\ProjectRepository;

class HomepageController
{
    /** @var BlogPostRepository */
    private $blogPostRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var string[][] */
    private $whoUsesDoctrine;

    /**
     * @param string[][] $whoUsesDoctrine
     */
    public function __construct(
        BlogPostRepository $blogPostRepository,
        ProjectRepository $projectRepository,
        array $whoUsesDoctrine
    ) {
        $this->blogPostRepository = $blogPostRepository;
        $this->projectRepository  = $projectRepository;
        $this->whoUsesDoctrine    = $whoUsesDoctrine;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        $blogPosts       = $this->blogPostRepository->findPaginated(1, 10);
        $primaryProjects = $this->projectRepository->findPrimaryProjects();

        return new ControllerResult([
            'blogPosts' => $blogPosts,
            'primaryProjects' => $primaryProjects,
            'whoUsesDoctrine' => $this->whoUsesDoctrine,
        ]);
    }
}
