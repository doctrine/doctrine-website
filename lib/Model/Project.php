<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Closure;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Repositories\ProjectRepository;
use InvalidArgumentException;

use function array_filter;
use function array_values;
use function sprintf;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    /**
     * @param Collection<int, ProjectVersion> $versions
     * @param string[]                        $keywords
     */
    public function __construct(
        #[ORM\OneToOne(targetEntity: ProjectStats::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(name: 'projectStats', referencedColumnName: 'id')]
        private ProjectStats $projectStats,
        #[ORM\Column(type: 'boolean')]
        private bool $active,
        #[ORM\Column(type: 'boolean')]
        private bool $archived,
        #[ORM\Column(type: 'string')]
        private string $name,
        #[ORM\Column(type: 'string')]
        private string $shortName,
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        private string $slug,
        #[ORM\Column(type: 'string')]
        private string $docsSlug,
        #[ORM\Column(type: 'string')]
        private string $composerPackageName,
        #[ORM\Column(type: 'string')]
        private string $repositoryName,
        #[ORM\Column(type: 'string')]
        private string $integrationFor,
        #[ORM\Column(type: 'string')]
        private string $docsRepositoryName,
        #[ORM\Column(type: 'string')]
        private string $docsPath,
        #[ORM\Column(type: 'string')]
        private string $codePath,
        #[ORM\Column(type: 'string')]
        private string $description,
        #[ORM\OneToOne(targetEntity: ProjectIntegrationType::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(name: 'projectIntegrationType', referencedColumnName: 'id', nullable: true)]
        private ProjectIntegrationType|null $projectIntegrationType,
        #[ORM\Column(type: 'boolean')]
        private bool $integration,
        #[ORM\Column(type: 'simple_array')]
        private array $keywords,
        #[ORM\OneToMany(targetEntity: ProjectVersion::class, fetch: 'EAGER', mappedBy: 'project')]
        private Collection $versions,
    ) {
        foreach ($this->versions as $version) {
            $version->setProject($this);
        }
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
        return $this->integration;
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

    /**
     * @phpstan-param Closure(ProjectVersion $version): bool $filter
     *
     * @return ProjectVersion[]
     */
    public function getVersions(Closure|null $filter = null): array
    {
        $versions = $this->versions->getValues();

        if ($filter !== null) {
            return array_values(array_filter($versions, $filter));
        }

        return $versions;
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
