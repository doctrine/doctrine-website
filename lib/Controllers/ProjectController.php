<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;

final readonly class ProjectController
{
    /**
     * @param ProjectRepository<Project>                       $projectRepository
     */
    public function __construct(
        private ProjectRepository $projectRepository,
    ) {
    }

    public function index(): Response
    {
        return new Response([
            'primaryProjects' => $this->projectRepository->findPrimaryProjects(),
            'inactiveProjects' => $this->projectRepository->findInactiveProjects(),
            'archivedProjects' => $this->projectRepository->findArchivedProjects(),
            'integrationProjects' => $this->projectRepository->findIntegrationProjects(),
        ]);
    }

    public function view(string $slug): Response
    {
        $project = $this->projectRepository->findOneBySlug($slug);

        return new Response([
            'project' => $project,
            'integrationProjects' => $this->projectRepository->findProjectIntegrations($project),
        ], '/project.html.twig');
    }

    public function version(string $slug, string $versionSlug): Response
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
