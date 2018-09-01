<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Blog\BlogPostRepository;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Projects\ProjectRepository;
use function array_slice;

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
        $blogPosts = array_slice($this->blogPostRepository->findAll(), 0, 9);
        $projects  = $this->projectRepository->findAll();

        return new ControllerResult([
            'blogPosts' => $blogPosts,
            'projects' => $projects,
            'whoUsesDoctrine' => $this->whoUsesDoctrine,
        ]);
    }
}
