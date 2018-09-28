<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Closure;
use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use InvalidArgumentException;
use function array_filter;
use function array_values;
use function sprintf;

class Project implements HydratableInterface, LoadMetadataInterface
{
    /** @var ProjectIntegrationType|null */
    private $projectIntegrationType;

    /** @var bool */
    private $active;

    /** @var bool */
    private $archived;

    /** @var string */
    private $name;

    /** @var string */
    private $shortName;

    /** @var string */
    private $slug;

    /** @var string */
    private $docsSlug;

    /** @var string */
    private $composerPackageName;

    /** @var string */
    private $repositoryName;

    /** @var bool */
    private $hasDocs = false;

    /** @var bool */
    private $isIntegration = false;

    /** @var string */
    private $integrationFor;

    /** @var string */
    private $docsRepositoryName;

    /** @var string */
    private $docsPath;

    /** @var string */
    private $codePath;

    /** @var string */
    private $description;

    /** @var string[] */
    private $keywords = [];

    /** @var ProjectVersion[] */
    private $versions = [];

    /**
     * @param mixed[] $project
     */
    public function __construct(array $project)
    {
        $this->doHydrate($project);
    }

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['slug']);
    }

    /**
     * @param mixed[] $project
     */
    public function hydrate(array $project, ObjectManagerInterface $objectManager) : void
    {
        $this->doHydrate($project);
    }

    /**
     * @param mixed[] $project
     */
    public function doHydrate(array $project) : void
    {
        $this->active              = (bool) ($project['active'] ?? true);
        $this->archived            = (bool) ($project['archived'] ?? false);
        $this->name                = (string) ($project['name'] ?? '');
        $this->shortName           = (string) ($project['shortName'] ?? $this->name);
        $this->slug                = (string) ($project['slug'] ?? '');
        $this->docsSlug            = (string) ($project['docsSlug'] ?? $this->slug);
        $this->composerPackageName = (string) ($project['composerPackageName'] ?? '');
        $this->repositoryName      = (string) ($project['repositoryName'] ?? '');
        $this->hasDocs             = (bool) ($project['hasDocs'] ?? true);
        $this->isIntegration       = (bool) ($project['integration'] ?? false);
        $this->integrationFor      = (string) ($project['integrationFor'] ?? '');
        $this->docsRepositoryName  = (string) ($project['docsRepositoryName'] ?? $this->repositoryName);
        $this->docsPath            = (string) ($project['docsPath'] ?? '/docs');
        $this->codePath            = (string) ($project['codePath'] ?? '/lib');
        $this->description         = (string) ($project['description'] ?? '');
        $this->keywords            = $project['keywords'] ?? [];

        if (! isset($project['versions'])) {
            return;
        }

        foreach ($project['versions'] as $version) {
            $this->versions[] = $version instanceof ProjectVersion
                ? $version
                : new ProjectVersion($version);
        }

        if (! $this->isIntegration) {
            return;
        }

        $this->projectIntegrationType = new ProjectIntegrationType($project['integrationType']);
    }

    public function getProjectIntegrationType() : ?ProjectIntegrationType
    {
        return $this->projectIntegrationType;
    }

    public function isActive() : bool
    {
        return $this->active;
    }

    public function isArchived() : bool
    {
        return $this->archived;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getShortName() : string
    {
        return $this->shortName;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    public function getDocsSlug() : string
    {
        return $this->docsSlug;
    }

    public function getComposerPackageName() : string
    {
        return $this->composerPackageName;
    }

    public function getRepositoryName() : string
    {
        return $this->repositoryName;
    }

    public function hasDocs() : bool
    {
        return $this->hasDocs;
    }

    public function isIntegration() : bool
    {
        return $this->isIntegration;
    }

    public function getIntegrationFor() : string
    {
        return $this->integrationFor;
    }

    public function getDocsRepositoryName() : string
    {
        return $this->docsRepositoryName;
    }

    public function getDocsPath() : string
    {
        return $this->docsPath;
    }

    public function getCodePath() : string
    {
        return $this->codePath;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function getKeywords() : array
    {
        return $this->keywords;
    }

    /**
     * @return ProjectVersion[]
     */
    public function getVersions(?Closure $filter = null) : array
    {
        if ($filter !== null) {
            return array_values(array_filter($this->versions, $filter));
        }

        return $this->versions;
    }

    /**
     * @return ProjectVersion[]
     */
    public function getMaintainedVersions() : array
    {
        return $this->getVersions(static function (ProjectVersion $version) {
            return $version->isMaintained();
        });
    }

    /**
     * @return ProjectVersion[]
     */
    public function getUnmaintainedVersions() : array
    {
        return $this->getVersions(static function (ProjectVersion $version) {
            return ! $version->isMaintained();
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getVersion(string $slug) : ProjectVersion
    {
        $projectVersion = $this->getVersions(static function (ProjectVersion $version) use ($slug) {
            return $version->getSlug() === $slug;
        })[0] ?? null;

        if ($projectVersion === null) {
            throw new InvalidArgumentException(sprintf('Could not find version %s for project %s', $slug, $this->slug));
        }

        return $projectVersion;
    }

    public function getCurrentVersion() : ?ProjectVersion
    {
        return $this->getVersions(static function (ProjectVersion $version) {
            return $version->isCurrent();
        })[0] ?? ($this->versions[0] ?? null);
    }

    public function getProjectDocsRepositoryPath(string $projectsPath) : string
    {
        return $projectsPath . '/' . $this->getDocsRepositoryName();
    }

    public function getProjectRepositoryPath(string $projectsPath) : string
    {
        return $projectsPath . '/' . $this->getRepositoryName();
    }

    public function getAbsoluteDocsPath(string $projectsPath) : string
    {
        return $this->getProjectDocsRepositoryPath($projectsPath) . $this->getDocsPath();
    }

    public function getProjectVersionDocsPath(string $docsPath, ProjectVersion $version, string $language) : string
    {
        return $docsPath . '/' . $this->getDocsSlug() . '/' . $language . '/' . $version->getSlug();
    }

    public function getProjectVersionDocsOutputPath(
        string $outputPath,
        ProjectVersion $version,
        string $language
    ) : string {
        return $outputPath . '/projects/' . $this->getDocsSlug() . '/' . $language . '/' . $version->getSlug();
    }
}
