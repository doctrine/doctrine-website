<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTLanguagesDetector;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Projects\ProjectVersion;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use function array_filter;
use function sprintf;

class BuildDocs
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ProjectGitSyncer */
    private $projectGitSyncer;

    /** @var APIBuilder */
    private $apiBuilder;

    /** @var RSTLanguagesDetector */
    private $rstLanguagesDetector;

    /** @var RSTBuilder */
    private $rstBuilder;

    /** @var SearchIndexer */
    private $searchIndexer;

    public function __construct(
        ProjectRepository $projectRepository,
        ProjectGitSyncer $projectGitSyncer,
        APIBuilder $apiBuilder,
        RSTLanguagesDetector $rstLanguagesDetector,
        RSTBuilder $rstBuilder,
        SearchIndexer $searchIndexer
    ) {
        $this->projectRepository    = $projectRepository;
        $this->projectGitSyncer     = $projectGitSyncer;
        $this->apiBuilder           = $apiBuilder;
        $this->rstLanguagesDetector = $rstLanguagesDetector;
        $this->rstBuilder           = $rstBuilder;
        $this->searchIndexer        = $searchIndexer;
    }

    public function build(
        OutputInterface $output,
        string $projectToBuild,
        string $versionToBuild,
        bool $buildApiDocs,
        bool $buildSearchIndexes,
        bool $syncGit
    ) : void {
        if ($buildSearchIndexes) {
            $this->searchIndexer->initSearchIndex();
        }

        foreach ($this->projectRepository->getProjectRepositoryNames() as $repositoryName) {
            $this->projectGitSyncer->initRepository($repositoryName);
        }

        $projects = $this->getProjectsToBuild($projectToBuild);

        foreach ($projects as $project) {
            foreach ($this->getProjectVersionsToBuild($project, $versionToBuild) as $version) {
                $output->writeln(sprintf(
                    '<info>%s</info> (<comment>%s</comment>)',
                    $project->getSlug(),
                    $version->getSlug()
                ));

                if ($syncGit) {
                    $output->writeln(' - syncing git');

                    $this->projectGitSyncer->sync($project, $version);
                }

                if ($buildApiDocs) {
                    $output->writeln(' - building api docs');

                    try {
                        $this->apiBuilder->buildAPIDocs($project, $version);
                    } catch (ProcessFailedException $e) {
                        $output->writeln(' - <error>building api docs failed</error>');
                        $output->writeln($e->getMessage());
                    }
                }

                $languages = $this->rstLanguagesDetector->detectLanguages($project, $version);

                $searchDocuments = [];

                foreach ($languages as $language) {
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

            if (! $syncGit) {
                continue;
            }

            $this->projectGitSyncer->checkoutMaster($project);
        }
    }

    /**
     * @return Project[]
     */
    private function getProjectsToBuild(string $projectToBuild) : array
    {
        return array_filter($this->projectRepository->findAll(), function (Project $project) use ($projectToBuild) : bool {
            if ($projectToBuild !== '' && $project->getSlug() !== $projectToBuild) {
                return false;
            }

            return true;
        });
    }

    /**
     * @return ProjectVersion[]
     */
    private function getProjectVersionsToBuild(Project $project, string $versionToBuild) : array
    {
        return array_filter($project->getVersions(), function (ProjectVersion $version) use ($versionToBuild) : bool {
            if ($versionToBuild !== '' && $version->getSlug() !== $versionToBuild) {
                return false;
            }

            return true;
        });
    }
}
