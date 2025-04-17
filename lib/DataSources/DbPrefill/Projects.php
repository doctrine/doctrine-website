<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources\DbPrefill;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\DataSources\DataSource;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectIntegrationType;
use Doctrine\Website\Model\ProjectStats;
use Doctrine\Website\Model\ProjectVersion;

use function version_compare;

class Projects implements DbPrefill
{
    public function __construct(private DataSource $dataSource, private EntityManagerInterface $entityManager)
    {
    }

    public function populate(): void
    {
        foreach ($this->dataSource->getSourceRows() as $sourceRow) {
            $this->buildAndSaveProject($sourceRow);
        }
    }

    /** @param mixed[] $projectData */
    private function buildAndSaveProject(array $projectData): void
    {
        $active              = (bool) ($projectData['active'] ?? true);
        $archived            = (bool) ($projectData['archived'] ?? false);
        $name                = (string) ($projectData['name'] ?? '');
        $shortName           = (string) ($projectData['shortName'] ?? $name);
        $slug                = (string) ($projectData['slug'] ?? '');
        $docsSlug            = (string) ($projectData['docsSlug'] ?? $slug);
        $composerPackageName = (string) ($projectData['composerPackageName'] ?? '');
        $repositoryName      = (string) ($projectData['repositoryName'] ?? '');
        $integration         = (bool) ($projectData['integration'] ?? false);
        $integrationFor      = (string) ($projectData['integrationFor'] ?? '');
        $docsRepositoryName  = (string) ($projectData['docsRepositoryName'] ?? $repositoryName);
        $docsPath            = (string) ($projectData['docsPath'] ?? '/docs');
        $description         = (string) ($projectData['description'] ?? '');
        $keywords            = $projectData['keywords'] ?? [];
        $sortOrder           = $projectData['sortOrder'] ?? 999;

        $versions = new ArrayCollection();
        foreach ($projectData['versions'] ?? [] as $version) {
            $projectVersion = new ProjectVersion($version);

            foreach ($version['tags'] ?? [] as $tag) {
                $tag = new Tag($tag['name'], new DateTimeImmutable($tag['date']));
                $projectVersion->addTag($tag);
            }

            $tagVersion = $projectVersion->getLatestTag()?->getName();
            if (isset($projectData['versionsGreaterThan']) && $tagVersion !== null && version_compare($projectData['versionsGreaterThan'], $tagVersion, '>')) {
                continue;
            }

            foreach ($version['docsLanguages'] ?? [] as $language) {
                $rstLanguage = new RSTLanguage($language['code'], $language['path']);
                $projectVersion->addDocsLanguage($rstLanguage);
            }

            $this->entityManager->persist($projectVersion);

            $versions->add($projectVersion);
        }

        $projectIntegrationType = null;
        if ($integration) {
            $projectIntegrationType = new ProjectIntegrationType(...$projectData['integrationType']);

            $this->entityManager->persist($projectIntegrationType);
        }

        $projectStats = new ProjectStats(
            (int) ($projectData['packagistData']['package']['github_stars'] ?? 0),
            (int) ($projectData['packagistData']['package']['github_watchers'] ?? 0),
            (int) ($projectData['packagistData']['package']['github_forks'] ?? 0),
            (int) ($projectData['packagistData']['package']['github_open_issues'] ?? 0),
            (int) ($projectData['packagistData']['package']['dependents'] ?? 0),
            (int) ($projectData['packagistData']['package']['suggesters'] ?? 0),
            (int) ($projectData['packagistData']['package']['downloads']['total'] ?? 0),
            (int) ($projectData['packagistData']['package']['downloads']['monthly'] ?? 0),
            (int) ($projectData['packagistData']['package']['downloads']['daily'] ?? 0),
        );
        $this->entityManager->persist($projectStats);

        $project = new Project(
            $projectStats,
            $active,
            $archived,
            $name,
            $shortName,
            $slug,
            $docsSlug,
            $composerPackageName,
            $repositoryName,
            $integrationFor,
            $docsRepositoryName,
            $docsPath,
            $description,
            $projectIntegrationType,
            $integration,
            $keywords,
            $versions,
            $sortOrder,
        );

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
}
