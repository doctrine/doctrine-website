<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Team\TeamRepository;

class ProjectController
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var TeamRepository */
    private $teamRepository;

    public function __construct(ProjectRepository $projectRepository, TeamRepository $teamRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->teamRepository    = $teamRepository;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'projects' => $this->projectRepository->findAll(),
        ]);
    }

    public function view(SourceFile $sourceFile) : ControllerResult
    {
        $project = $this->projectRepository->findOneBySlug($sourceFile->getParameter('docsSlug'));

        return new ControllerResult([
            'project' => $project,
            'allTeamMembers' => $this->teamRepository->getAllProjectTeamMembers($project),
            'activeTeamMembers' => $this->teamRepository->getActiveProjectTeamMembers($project),
            'inactiveTeamMembers' => $this->teamRepository->getInactiveProjectTeamMembers($project),
        ]);
    }
}
