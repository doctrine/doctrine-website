<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
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

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'primaryProjects' => $this->projectRepository->findPrimaryProjects(),
            'inactiveProjects' => $this->projectRepository->findInactiveProjects(),
            'archivedProjects' => $this->projectRepository->findArchivedProjects(),
            'integrationProjects' => $this->projectRepository->findIntegrationProjects(),
        ]);
    }

    public function view(SourceFile $sourceFile) : ControllerResult
    {
        $project = $this->projectRepository->findOneByDocsSlug($sourceFile->getParameter('docsSlug'));

        return new ControllerResult([
            'project' => $project,
            'integrationProjects' => $this->projectRepository->findProjectIntegrations($project),
            'maintainers' => $this->projectContributorRepository->findMaintainersByProject($project),
            'contributors' => $this->projectContributorRepository->findContributorsByProject($project),
        ]);
    }
}
