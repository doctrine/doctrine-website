<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Team\TeamRepository;

class TeamController
{
    /** @var TeamRepository */
    private $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'activeCoreTeamMembers' => $this->teamRepository->getActiveCoreTeamMembers(),
            'activeDocumentationTeamMembers' => $this->teamRepository->getActiveDocumentationTeamMembers(),
            'inactiveTeamMembers' => $this->teamRepository->getInactiveTeamMembers(),
        ]);
    }
}
