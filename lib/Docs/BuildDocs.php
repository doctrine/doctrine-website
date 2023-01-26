<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTLanguagesDetector;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Repositories\ProjectRepository;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

use function array_filter;
use function count;
use function sprintf;

class BuildDocs
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private ProjectGitSyncer $projectGitSyncer,
        private RSTBuilder $rstBuilder,
        private SearchIndexer $searchIndexer,
    ) {
    }

    public function build(
        OutputInterface $output,
        string $projectToBuild,
        string $versionToBuild,
        bool $buildSearchIndexes,
    ): void {
        if ($buildSearchIndexes) {
            $this->searchIndexer->initSearchIndex();
        }

        $projects = $this->projectRepository->findAll();

        $projectsToBuild = $this->getProjectsToBuild($projects, $projectToBuild);

        foreach ($projectsToBuild as $project) {
            foreach ($this->getProjectVersionsToBuild($project, $versionToBuild) as $version) {
                $docsLanguages = $version->getDocsLanguages();

                $shouldBuildDocs = count($docsLanguages) > 0;

                if ($shouldBuildDocs === false) {
                    $output->writeln(sprintf(
                        'Nothing to build for <info>%s</info> (<comment>%s</comment>)',
                        $project->getSlug(),
                        $version->getSlug(),
                    ));

                    continue;
                }

                $output->writeln(sprintf(
                    '<info>%s</info> (<comment>%s</comment>)',
                    $project->getSlug(),
                    $version->getSlug(),
                ));

                $output->writeln(sprintf(' - checking out %s', $version->getName()));

                if ($version->hasBranchName()) {
                    $this->projectGitSyncer->checkoutBranch(
                        $project->getRepositoryName(),
                        $version->getBranchName(),
                    );
                } else {
                    if (! $version->hasTags()) {
                        throw new UnexpectedValueException(
                            sprintf('Version %s has neither branchname nor tag', $version->getSlug()),
                        );
                    }

                    $this->projectGitSyncer->checkoutTag(
                        $project->getRepositoryName(),
                        $version->getLatestTag()->getName(),
                    );
                }

                $searchDocuments = [];

                foreach ($docsLanguages as $language) {
                    $output->writeln(sprintf(' - building %s rst docs', $language->getCode()));

                    $documents = $this->rstBuilder->buildRSTDocs($project, $version, $language);

                    if ($language->getCode() !== RSTLanguagesDetector::ENGLISH_LANGUAGE_CODE) {
                        continue;
                    }

                    $searchDocuments = $documents;
                }

                if (! $buildSearchIndexes) {
                    continue;
                }

                $output->writeln(' - building search indexes');

                $this->searchIndexer->buildSearchIndexes($project, $version, $searchDocuments);
            }

            $this->projectGitSyncer->checkoutDefaultBranch($project->getRepositoryName());
        }
    }

    /**
     * @param Project[] $projects
     *
     * @return Project[]
     */
    private function getProjectsToBuild(array $projects, string $projectToBuild): array
    {
        return array_filter($projects, static function (Project $project) use ($projectToBuild): bool {
            if ($projectToBuild !== '') {
                if ($project->getSlug() === $projectToBuild) {
                    return true;
                }

                return $project->getRepositoryName() === $projectToBuild;
            }

            return true;
        });
    }

    /** @return ProjectVersion[] */
    private function getProjectVersionsToBuild(Project $project, string $versionToBuild): array
    {
        return array_filter($project->getVersions(), static function (ProjectVersion $version) use ($versionToBuild): bool {
            return $versionToBuild === '' || $version->getSlug() === $versionToBuild;
        });
    }
}
