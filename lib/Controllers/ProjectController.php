<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\ProjectContributorRepository;
use Doctrine\Website\Repositories\ProjectRepository;

class ProjectController
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ProjectContributorRepository */
    private $projectContributorRepository;

    public function __construct(
        ProjectRepository $projectRepository,
        ProjectContributorRepository $projectContributorRepository
    ) {
        $this->projectRepository            = $projectRepository;
        $this->projectContributorRepository = $projectContributorRepository;
    }

    public function index() : Response
    {
        return new Response([
            'primaryProjects' => $this->projectRepository->findPrimaryProjects(),
            'inactiveProjects' => $this->projectRepository->findInactiveProjects(),
            'archivedProjects' => $this->projectRepository->findArchivedProjects(),
            'integrationProjects' => $this->projectRepository->findIntegrationProjects(),
        ]);
    }

    public function view(string $slug) : Response
    {
        $project = $this->projectRepository->findOneBySlug($slug);

        return new Response([
            'project' => $project,
            'integrationProjects' => $this->projectRepository->findProjectIntegrations($project),
            'maintainers' => $this->projectContributorRepository->findMaintainersByProject($project),
            'contributors' => $this->projectContributorRepository->findContributorsByProject($project),
        ], '/project.html.twig');
    }

    public function version(string $slug, string $versionSlug) : Response
    {
        $project = $this->projectRepository->findOneBySlug($slug);

        $version = $project->getVersion($versionSlug);

        return new Response([
            'project' => $project,
            'version' => $version,
            'latestTag' => $version->getLatestTag(),
        ], '/project-version.html.twig');
    }
}
