<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\ProcessFactory;
use function is_dir;
use function sprintf;

class ProjectGitSyncer
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $projectsDir;

    public function __construct(ProcessFactory $processFactory, string $projectsDir)
    {
        $this->processFactory = $processFactory;
        $this->projectsDir    = $projectsDir;
    }

    public function isRepositoryInitialized(string $repositoryName) : bool
    {
        return is_dir($this->projectsDir . '/' . $repositoryName);
    }

    public function initRepository(string $repositoryName) : void
    {
        $repositoryPath = $this->projectsDir . '/' . $repositoryName;

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

    public function sync(Project $project) : void
    {
        // handle when docs are in a different repository then the code
        if ($project->getDocsRepositoryName() !== $project->getRepositoryName()) {
            $this->syncRepository(
                $project->getProjectRepositoryPath($this->projectsDir)
            );
        }

        // sync docs repository
        $this->syncRepository(
            $project->getProjectDocsRepositoryPath($this->projectsDir)
        );
    }

    public function checkoutMaster(Project $project) : void
    {
        $this->checkoutBranch($project, 'master');
    }

    public function checkoutProjectVersion(Project $project, ProjectVersion $version) : void
    {
        $this->checkoutBranch($project, $version->getBranchName());
    }

    private function checkoutBranch(Project $project, string $branchName) : void
    {
        if ($project->getDocsRepositoryName() !== $project->getRepositoryName()) {
            $this->doCheckoutBranch($project->getProjectRepositoryPath($this->projectsDir), $branchName);
        }

        $this->doCheckoutBranch($project->getProjectDocsRepositoryPath($this->projectsDir), $branchName);
    }

    private function doCheckoutBranch(string $directory, string $branchName) : void
    {
        $command = sprintf(
            'cd %s && git clean -xdf && git checkout origin/%s',
            $directory,
            $branchName
        );

        $this->processFactory->run($command);
    }

    private function syncRepository(string $directory) : void
    {
        $command = sprintf(
            'cd %s && git clean -xdf && git fetch origin',
            $directory
        );

        $this->processFactory->run($command);
    }
}
