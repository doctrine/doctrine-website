<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\StaticGenerator\Controller\Response;

final readonly class DocumentationController
{
    /** @param ProjectRepository<Project> $projectRepository */
    public function __construct(
        private ProjectRepository $projectRepository,
    ) {
    }

    public function view(string $docsSlug, string $docsVersion): Response
    {
        $project = $this->projectRepository->findOneByDocsSlug($docsSlug);

        return new Response([
            'project' => $project,
            'projectVersion' => $project->getVersion($docsVersion),
        ]);
    }
}
