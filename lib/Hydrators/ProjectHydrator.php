<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectIntegrationType;
use Doctrine\Website\Model\ProjectStats;
use Doctrine\Website\Model\ProjectVersion;

/**
 * @property bool $active
 * @property bool $archived
 * @property string $name
 * @property string $shortName
 * @property string $slug
 * @property string $docsSlug
 * @property string $composerPackageName
 * @property string $repositoryName
 * @property bool $isIntegration
 * @property string $integrationFor
 * @property string $docsRepositoryName
 * @property string $docsPath
 * @property string $codePath
 * @property string $description
 * @property string[] $keywords
 * @property ProjectVersion[] $versions
 * @property ProjectIntegrationType $projectIntegrationType
 * @property ProjectStats $projectStats
 */
final class ProjectHydrator extends ModelHydrator
{
    protected function getClassName(): string
    {
        return Project::class;
    }

    /**
     * @param mixed[] $data
     */
    protected function doHydrate(array $data): void
    {
        $this->active              = (bool) ($data['active'] ?? true);
        $this->archived            = (bool) ($data['archived'] ?? false);
        $this->name                = (string) ($data['name'] ?? '');
        $this->shortName           = (string) ($data['shortName'] ?? $this->name);
        $this->slug                = (string) ($data['slug'] ?? '');
        $this->docsSlug            = (string) ($data['docsSlug'] ?? $this->slug);
        $this->composerPackageName = (string) ($data['composerPackageName'] ?? '');
        $this->repositoryName      = (string) ($data['repositoryName'] ?? '');
        $this->isIntegration       = (bool) ($data['integration'] ?? false);
        $this->integrationFor      = (string) ($data['integrationFor'] ?? '');
        $this->docsRepositoryName  = (string) ($data['docsRepositoryName'] ?? $this->repositoryName);
        $this->docsPath            = (string) ($data['docsPath'] ?? '/docs');
        $this->codePath            = (string) ($data['codePath'] ?? '/lib');
        $this->description         = (string) ($data['description'] ?? '');
        $this->keywords            = $data['keywords'] ?? [];

        if (! isset($data['versions'])) {
            return;
        }

        $versions = [];

        foreach ($data['versions'] as $version) {
            $versions[] = $version instanceof ProjectVersion
                ? $version
                : new ProjectVersion($version);
        }

        $this->versions = $versions;

        if ($this->isIntegration) {
            $this->projectIntegrationType = new ProjectIntegrationType($data['integrationType']);
        }

        $this->projectStats = new ProjectStats(
            (int) ($data['packagistData']['package']['github_stars'] ?? 0),
            (int) ($data['packagistData']['package']['github_watchers'] ?? 0),
            (int) ($data['packagistData']['package']['github_forks'] ?? 0),
            (int) ($data['packagistData']['package']['github_open_issues'] ?? 0),
            (int) ($data['packagistData']['package']['dependents'] ?? 0),
            (int) ($data['packagistData']['package']['suggesters'] ?? 0),
            (int) ($data['packagistData']['package']['downloads']['total'] ?? 0),
            (int) ($data['packagistData']['package']['downloads']['monthly'] ?? 0),
            (int) ($data['packagistData']['package']['downloads']['daily'] ?? 0)
        );
    }
}
