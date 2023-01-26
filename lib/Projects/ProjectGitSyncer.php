<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\ProcessFactory;
use Github\Api\Repo;

use function escapeshellarg;
use function is_dir;
use function sprintf;

class ProjectGitSyncer
{
    private Repo $githubRepo;

    public function __construct(private ProcessFactory $processFactory, GithubClientProvider $githubClientProvider, private string $projectsDir)
    {
        // TODO Inject Repo instead of GithubClientProvider
        $this->githubRepo = $githubClientProvider->getGithubClient()->repo();
    }

    public function isRepositoryInitialized(string $repositoryName): bool
    {
        return is_dir($this->projectsDir . '/' . $repositoryName . '/.git');
    }

    public function initRepository(string $repositoryName): void
    {
        $repositoryPath = $this->projectsDir . '/' . $repositoryName;

        if (is_dir($repositoryPath)) {
            return;
        }

        $command = sprintf(
            'git clone https://github.com/doctrine/%s.git %s',
            escapeshellarg($repositoryName),
            escapeshellarg($repositoryPath),
        );

        $this->processFactory->run($command);
    }

    public function checkoutDefaultBranch(string $repositoryName): void
    {
        $repoMetaData = $this->githubRepo->show('doctrine', $repositoryName);

        $this->checkoutBranch($repositoryName, $repoMetaData['default_branch']);
    }

    public function checkoutBranch(string $repositoryName, string $branchName): void
    {
        $command = sprintf(
            'cd %s && git clean -xdf && git checkout origin/%s',
            escapeshellarg($this->getRepositoryPath($repositoryName)),
            escapeshellarg($branchName),
        );

        $this->processFactory->run($command);
    }

    public function checkoutTag(string $repositoryName, string $tagName): void
    {
        $command = sprintf(
            'cd %s && git clean -xdf && git checkout tags/%s',
            escapeshellarg($this->getRepositoryPath($repositoryName)),
            escapeshellarg($tagName),
        );

        $this->processFactory->run($command);
    }

    public function syncRepository(string $repositoryName): void
    {
        $command = sprintf(
            'cd %s && git clean -xdf && git fetch origin',
            escapeshellarg($this->getRepositoryPath($repositoryName)),
        );

        $this->processFactory->run($command);
    }

    private function getRepositoryPath(string $repositoryName): string
    {
        return $this->projectsDir . '/' . $repositoryName;
    }
}
