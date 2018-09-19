<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Repositories\TeamMemberRepository;

class ProjectController
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    public function __construct(ProjectRepository $projectRepository, TeamMemberRepository $teamMemberRepository)
    {
        $this->projectRepository    = $projectRepository;
        $this->teamMemberRepository = $teamMemberRepository;
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
            'allTeamMembers' => $this->teamMemberRepository->getAllProjectTeamMembers($project),
            'activeTeamMembers' => $this->teamMemberRepository->getActiveProjectTeamMembers($project),
            'inactiveTeamMembers' => $this->teamMemberRepository->getInactiveProjectTeamMembers($project),
            'integrationProjects' => $this->projectRepository->findProjectIntegrations($project),
        ]);
    }
}
