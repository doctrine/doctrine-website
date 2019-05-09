<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\TeamMemberRepository;

class ConsultingController
{
    /** @var TeamMemberRepository */
    private $teamMemberRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }

    public function index() : Response
    {
        $consultants = $this->teamMemberRepository->findConsultants();

        return new Response(
            ['consultants' => $consultants]
        );
    }
}
