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

    public function maintainers(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'contributors' => $this->contributorRepository->findMaintainers(),
        ]);
    }

    public function contributors(SourceFile $sourceFile) : ControllerResult
    {
        return new ControllerResult([
            'contributors' => $this->contributorRepository->findContributors(),
        ]);
    }
}
