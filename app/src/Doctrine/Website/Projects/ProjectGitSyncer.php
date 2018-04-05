<?php

namespace Doctrine\Website\Projects;

use Doctrine\Website\ProcessFactory;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProjectGitSyncer
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $projectsPath;

    public function __construct(ProcessFactory $processFactory, string $projectsPath)
    {
        $this->processFactory = $processFactory;
        $this->projectsPath = $projectsPath;
    }

    public function sync(
        Project $project,
        ProjectVersion $version)
    {
        if (!is_dir($this->projectsPath)) {
            mkdir($this->projectsPath, 0777, true);
        }

        $dir = $project->getProjectDocsRepositoryPath($this->projectsPath);

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

    private function syncRepository(
        string $repositoryName,
        string $branchName,
        string $dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);

            $command = sprintf('git clone git@github.com:doctrine/%s.git %s',
                $repositoryName,
                $dir
            );

            $this->execute($command);
        }

        $command = sprintf('cd %s && git fetch && git clean -f && git reset --hard origin/master && git checkout %s',
            $dir, $branchName
        );

        $this->execute($command);
    }

    private function execute(string $command)
    {
        $process = $this->processFactory->run($command);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
