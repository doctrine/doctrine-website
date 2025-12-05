<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\Projects\GetProjectPackagistData;
use Doctrine\Website\Projects\ProjectDataReader;
use Doctrine\Website\Projects\ProjectDataRepository;
use Doctrine\Website\Projects\ProjectGitSyncer;

use function array_filter;
use function array_map;
use function array_replace;

final readonly class Projects implements DataSource
{
    private const array DEFAULTS = [
        'active'        => true,
        'archived'      => false,
        'integration'   => false,
    ];

    public function __construct(
        private ProjectDataRepository $projectDataRepository,
        private ProjectGitSyncer $projectGitSyncer,
        private ProjectDataReader $projectDataReader,
        private GetProjectPackagistData $getProjectPackagistData,
        private ProjectVersions $projectVersions,
    ) {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        $repositoryNames       = $this->projectDataRepository->getProjectRepositoryNames();
        $clonedRepositoryNames = array_filter($repositoryNames, fn (string $repositoryName): bool => $this->projectGitSyncer->isRepositoryInitialized($repositoryName));

        return array_map(function (string $repositoryName): array {
            return $this->buildProjectData($repositoryName);
        }, $clonedRepositoryNames);
    }

    /** @return mixed[] */
    private function buildProjectData(string $repositoryName): array
    {
        $this->projectGitSyncer->checkoutDefaultBranch($repositoryName);

        $projectData = array_replace(
            self::DEFAULTS,
            $this->projectDataReader->read($repositoryName),
        );

        $projectData['versions'] = $this->projectVersions->buildProjectVersions(
            $repositoryName,
            $projectData,
        );

        $projectData['packagistData'] = $this->getProjectPackagistData->__invoke(
            $projectData['composerPackageName'],
        );

        return $projectData;
    }
}
