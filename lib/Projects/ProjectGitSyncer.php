<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\ProcessFactory;
use function is_dir;
use function sprintf;

class ProjectGitSyncer
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $projectsPath;

    public function __construct(ProcessFactory $processFactory, string $projectsPath)
    {
        $this->processFactory = $processFactory;
        $this->projectsPath   = $projectsPath;
    }

    public function initRepository(string $repositoryName) : void
    {
        $repositoryPath = $this->projectsPath . '/' . $repositoryName;

        if (is_dir($repositoryPath)) {
            return;
        }

        $command = sprintf(
            'git clone https://github.com/doctrine/%s.git %s',
            $repositoryName,
            $repositoryPath
        );

        $this->processFactory->run($command);
    }

    public function sync(
        Project $project,
        ProjectVersion $version
    ) : void {
        // handle when docs are in a different repository then the code
        if ($project->getDocsRepositoryName() !== $project->getRepositoryName()) {
            $this->syncRepository(
                $project->getRepositoryName(),
                $version->getBranchName(),
                $project->getProjectRepositoryPath($this->projectsPath)
            );
        }

        // sync docs repository
        $this->syncRepository(
            $project->getDocsRepositoryName(),
            $version->getBranchName(),
            $project->getProjectDocsRepositoryPath($this->projectsPath)
        );
    }

    public function checkoutMaster(Project $project) : void
    {
        $this->syncRepository(
            $project->getRepositoryName(),
            'master',
            $project->getProjectRepositoryPath($this->projectsPath)
        );
    }

    private function syncRepository(
        string $repositoryName,
        string $branchName,
        string $dir
    ) : void {
        $this->initRepository($repositoryName);

        $command = sprintf(
            'cd %s && git clean -xdf && git fetch origin && git checkout origin/%s',
            $dir,
            $branchName
        );

        $this->processFactory->run($command);
    }
}
