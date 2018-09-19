<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Repositories\TeamMemberRepository;

class TeamController
{
    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'activeCoreTeamMembers' => $this->teamMemberRepository->getActiveCoreTeamMembers(),
            'activeDocumentationTeamMembers' => $this->teamMemberRepository->getActiveDocumentationTeamMembers(),
            'inactiveTeamMembers' => $this->teamMemberRepository->getInactiveTeamMembers(),
        ]);
    }
}
