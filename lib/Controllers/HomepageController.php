<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\BlogPostRepository;
use Doctrine\Website\Repositories\DoctrineUserRepository;
use Doctrine\Website\Repositories\PartnerRepository;
use Doctrine\Website\Repositories\ProjectRepository;

class HomepageController
{
    /** @var BlogPostRepository */
    private $blogPostRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var DoctrineUserRepository */
    private $doctrineUserRepository;

    /** @var PartnerRepository */
    private $partnerRepository;

    public function __construct(
        BlogPostRepository $blogPostRepository,
        ProjectRepository $projectRepository,
        DoctrineUserRepository $doctrineUserRepository,
        PartnerRepository $partnerRepository
    ) {
        $this->blogPostRepository     = $blogPostRepository;
        $this->projectRepository      = $projectRepository;
        $this->doctrineUserRepository = $doctrineUserRepository;
        $this->partnerRepository      = $partnerRepository;
    }

    public function index() : Response
    {
        $blogPosts       = $this->blogPostRepository->findPaginated(1, 10);
        $primaryProjects = $this->projectRepository->findPrimaryProjects();
        $doctrineUsers   = $this->doctrineUserRepository->findAll();
        $featuredPartner = $this->partnerRepository->findFeaturedPartner();

        return new Response([
            'blogPosts' => $blogPosts,
            'primaryProjects' => $primaryProjects,
            'doctrineUsers' => $doctrineUsers,
            'featuredPartner' => $featuredPartner,
        ]);
    }
}
