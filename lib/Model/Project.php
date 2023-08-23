<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Closure;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use InvalidArgumentException;

use function array_filter;
use function array_values;
use function sprintf;

class Project implements LoadMetadataInterface
{
    private ProjectIntegrationType|null $projectIntegrationType = null;

    private ProjectStats $projectStats;

    private bool $active;

    private bool $archived;

    private string $name;

    private string $shortName;

    private string $slug;

    private string $docsSlug;

    private string $composerPackageName;

    private string $repositoryName;

    private bool $isIntegration = false;

    private string $integrationFor;

    private string $docsRepositoryName;

    private string $docsPath;

    private string $codePath;

    private string $description;

    /** @var string[] */
    private array $keywords = [];

    /** @var ProjectVersion[] */
    private array $versions = [];

    public static function loadMetadata(ClassMetadataInterface $metadata): void
    {
        $metadata->setIdentifier(['slug']);
    }

    public function getProjectIntegrationType(): ProjectIntegrationType|null
    {
        return $this->projectIntegrationType;
    }

    public function getProjectStats(): ProjectStats
    {
        return $this->projectStats;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDocsSlug(): string
    {
        return $this->docsSlug;
    }

    public function getComposerPackageName(): string
    {
        return $this->composerPackageName;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function isIntegration(): bool
    {
        return $this->isIntegration;
    }

    public function getIntegrationFor(): string
    {
        return $this->integrationFor;
    }

    public function getDocsRepositoryName(): string
    {
        return $this->docsRepositoryName;
    }

    public function getDocsPath(): string
    {
        return $this->docsPath;
    }

    public function getCodePath(): string
    {
        return $this->codePath;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /** @return string[] */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /** @return ProjectVersion[] */
    public function getVersions(Closure|null $filter = null): array
    {
        if ($filter !== null) {
            return array_values(array_filter($this->versions, $filter));
        }

        return $this->versions;
    }

    /** @return ProjectVersion[] */
    public function getMaintainedVersions(): array
    {
        return $this->getVersions(static function (ProjectVersion $version): bool {
            return $version->isMaintained();
        });
    }

    /** @return ProjectVersion[] */
    public function getUnmaintainedVersions(): array
    {
        return $this->getVersions(static function (ProjectVersion $version): bool {
            return ! $version->isMaintained();
        });
    }

    /** @throws InvalidArgumentException */
    public function getVersion(string $slug): ProjectVersion
    {
        $projectVersion = $this->getVersions(static function (ProjectVersion $version) use ($slug): bool {
            return $version->getSlug() === $slug;
        })[0] ?? null;

        if ($projectVersion === null) {
            throw new InvalidArgumentException(sprintf('Could not find version %s for project %s', $slug, $this->slug));
        }

        return $projectVersion;
    }

    public function getCurrentVersion(): ProjectVersion|null
    {
        return $this->getVersions(static function (ProjectVersion $version): bool {
            return $version->isCurrent();
        })[0] ?? ($this->versions[0] ?? null);
    }

    public function getProjectDocsRepositoryPath(string $projectsDir): string
    {
        return $projectsDir . '/' . $this->getDocsRepositoryName();
    }

    public function getProjectRepositoryPath(string $projectsDir): string
    {
        return $projectsDir . '/' . $this->getRepositoryName();
    }

    public function getAbsoluteDocsPath(string $projectsDir): string
    {
        return $this->getProjectDocsRepositoryPath($projectsDir) . $this->getDocsPath();
    }

    public function getProjectVersionDocsPath(string $docsPath, ProjectVersion $version, string $language): string
    {
        return $docsPath . '/' . $this->getDocsSlug() . '/' . $language . '/' . $version->getSlug();
    }

    public function getProjectVersionDocsOutputPath(
        string $outputPath,
        ProjectVersion $version,
        string $language,
    ): string {
        return $outputPath . '/projects/' . $this->getDocsSlug() . '/' . $language . '/' . $version->getSlug();
    }
}
