<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Model\BlogPost;
use Doctrine\Website\Model\DoctrineUser;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Projects\GetTotalDownloads;
use Doctrine\Website\Repositories\BlogPostRepository;
use Doctrine\Website\Repositories\DoctrineUserRepository;
use Doctrine\Website\Repositories\PartnerRepository;
use Doctrine\Website\Repositories\ProjectRepository;

final readonly class HomepageController
{
    /**
     * @param BlogPostRepository<BlogPost>         $blogPostRepository
     * @param ProjectRepository<Project>           $projectRepository
     * @param DoctrineUserRepository<DoctrineUser> $doctrineUserRepository
     * @param PartnerRepository<Partner>           $partnerRepository
     */
    public function __construct(
        private BlogPostRepository $blogPostRepository,
        private ProjectRepository $projectRepository,
        private DoctrineUserRepository $doctrineUserRepository,
        private PartnerRepository $partnerRepository,
        private GetTotalDownloads $getTotalDownloads,
    ) {
    }

    public function index(): Response
    {
        $blogPosts       = $this->blogPostRepository->findPaginated(1, 10);
        $primaryProjects = $this->projectRepository->findPrimaryProjects();
        $doctrineUsers   = $this->doctrineUserRepository->findAll();
        $featuredPartner = $this->partnerRepository->findFeaturedPartner();
        $totalDownloads  = $this->getTotalDownloads->__invoke();

        return new Response([
            'blogPosts' => $blogPosts,
            'primaryProjects' => $primaryProjects,
            'doctrineUsers' => $doctrineUsers,
            'featuredPartner' => $featuredPartner,
            'totalDownloads' => $totalDownloads,
        ]);
    }
}
