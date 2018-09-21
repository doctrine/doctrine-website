<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Repositories\ContributorRepository;

class TeamController
{
    /** @var ContributorRepository */
    private $contributorRepository;

    public function __construct(ContributorRepository $contributorRepository)
    {
        $this->contributorRepository = $contributorRepository;
    }

    public function index(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'coreContributors' => $this->contributorRepository->findCoreContributors(),
            'contributors' => $this->contributorRepository->findContributors(),
        ]);
    }
}
