<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Controller\ControllerResult;
use Doctrine\Website\Repositories\ProjectRepository;

class DocumentationController
{
    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function view(SourceFile $sourceFile) : ControllerResult
    {
        $project = $this->projectRepository->findOneByDocsSlug($sourceFile->getParameter('docsSlug'));

        return new ControllerResult([
            'project' => $project,
            'projectVersion' => $project->getVersion($sourceFile->getParameter('docsVersion')),
        ]);
    }
}
