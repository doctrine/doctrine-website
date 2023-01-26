<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\ProjectRepository;

class DocumentationController
{
    public function __construct(private ProjectRepository $projectRepository)
    {
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
