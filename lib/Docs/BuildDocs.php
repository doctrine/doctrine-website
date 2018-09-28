<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTLanguagesDetector;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Projects\ProjectDataRepository;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Repositories\ProjectRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use function array_filter;
use function sprintf;

class BuildDocs
{
    /** @var ProjectDataRepository */
    private $projectDataRepository;

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
        ProjectDataRepository $projectDataRepository,
        ProjectRepository $projectRepository,
        ProjectGitSyncer $projectGitSyncer,
        APIBuilder $apiBuilder,
        RSTLanguagesDetector $rstLanguagesDetector,
        RSTBuilder $rstBuilder,
        SearchIndexer $searchIndexer
    ) {
        $this->projectDataRepository = $projectDataRepository;
        $this->projectRepository     = $projectRepository;
        $this->projectGitSyncer      = $projectGitSyncer;
        $this->apiBuilder            = $apiBuilder;
        $this->rstLanguagesDetector  = $rstLanguagesDetector;
        $this->rstBuilder            = $rstBuilder;
        $this->searchIndexer         = $searchIndexer;
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

        foreach ($this->projectDataRepository->getProjectRepositoryNames() as $repositoryName) {
            if ($this->projectGitSyncer->isRepositoryInitialized($repositoryName)) {
                continue;
            }

            $output->writeln(sprintf('Initializing <info>%s</info> repository', $repositoryName));

            $this->projectGitSyncer->initRepository($repositoryName);
        }

        $projects = $this->projectRepository->findAll();

        $this->initDocsRepositories($output, $projects);

        $projectsToBuild = $this->getProjectsToBuild($projects, $projectToBuild);

        foreach ($projectsToBuild as $project) {
            foreach ($this->getProjectVersionsToBuild($project, $versionToBuild) as $version) {
                $output->writeln(sprintf(
                    '<info>%s</info> (<comment>%s</comment>)',
                    $project->getSlug(),
                    $version->getSlug()
                ));

                if ($syncGit) {
                    $output->writeln(' - syncing git');

                    $this->projectGitSyncer->sync($project);
                }

                $output->writeln(sprintf(' - checking out %s', $version->getName()));

                $this->projectGitSyncer->checkoutProjectVersion($project, $version);

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

            $this->projectGitSyncer->checkoutMaster($project);
        }
    }

    /**
     * @param Project[] $projects
     */
    private function initDocsRepositories(OutputInterface $output, array $projects) : void
    {
        foreach ($projects as $project) {
            if ($project->getRepositoryName() === $project->getDocsRepositoryName()) {
                continue;
            }

            $repositoryName = $project->getDocsRepositoryName();

            if ($this->projectGitSyncer->isRepositoryInitialized($repositoryName)) {
                continue;
            }

            $output->writeln(sprintf('Initializing <info>%s</info> repository', $repositoryName));

            $this->projectGitSyncer->initRepository($repositoryName);
        }
    }

    /**
     * @param Project[] $projects
     *
     * @return Project[]
     */
    private function getProjectsToBuild(array $projects, string $projectToBuild) : array
    {
        return array_filter($projects, static function (Project $project) use ($projectToBuild) : bool {
            if ($projectToBuild !== '') {
                if ($project->getSlug() === $projectToBuild) {
                    return true;
                }

                if ($project->getRepositoryName() === $projectToBuild) {
                    return true;
                }

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
        return array_filter($project->getVersions(), static function (ProjectVersion $version) use ($versionToBuild) : bool {
            if ($versionToBuild !== '' && $version->getSlug() !== $versionToBuild) {
                return false;
            }

            return true;
        });
    }
}
