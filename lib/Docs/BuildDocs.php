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
use Symfony\Component\Process\Exception\ProcessFailedException;
use function array_filter;
use function count;
use function sprintf;

class BuildDocs
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ProjectGitSyncer */
    private $projectGitSyncer;

    /** @var APIBuilder */
    private $apiBuilder;

    /** @var RSTBuilder */
    private $rstBuilder;

    /** @var SearchIndexer */
    private $searchIndexer;

    public function __construct(
        ProjectRepository $projectRepository,
        ProjectGitSyncer $projectGitSyncer,
        APIBuilder $apiBuilder,
        RSTBuilder $rstBuilder,
        SearchIndexer $searchIndexer
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectGitSyncer  = $projectGitSyncer;
        $this->apiBuilder        = $apiBuilder;
        $this->rstBuilder        = $rstBuilder;
        $this->searchIndexer     = $searchIndexer;
    }

    public function build(
        OutputInterface $output,
        string $projectToBuild,
        string $versionToBuild,
        bool $buildApiDocs,
        bool $buildSearchIndexes
    ) : void {
        if ($buildSearchIndexes) {
            $this->searchIndexer->initSearchIndex();
        }

        $projects = $this->projectRepository->findAll();

        $projectsToBuild = $this->getProjectsToBuild($projects, $projectToBuild);

        foreach ($projectsToBuild as $project) {
            foreach ($this->getProjectVersionsToBuild($project, $versionToBuild) as $version) {
                $shouldBuildDocs = $buildApiDocs || count($version->getDocsLanguages()) > 0;

                if ($shouldBuildDocs === false) {
                    $output->writeln(sprintf(
                        'Nothing to build for <info>%s</info> (<comment>%s</comment>)',
                        $project->getSlug(),
                        $version->getSlug()
                    ));

                    continue;
                }

                $output->writeln(sprintf(
                    '<info>%s</info> (<comment>%s</comment>)',
                    $project->getSlug(),
                    $version->getSlug()
                ));

                $output->writeln(sprintf(' - checking out %s', $version->getName()));

                $this->projectGitSyncer->checkoutBranch(
                    $project->getRepositoryName(),
                    $version->getBranchName()
                );

                if ($buildApiDocs) {
                    $output->writeln(' - building api docs');

                    try {
                        $this->apiBuilder->buildAPIDocs($project, $version);
                    } catch (ProcessFailedException $e) {
                        $output->writeln(' - <error>building api docs failed</error>');
                        $output->writeln($e->getMessage());
                    }
                }

                $searchDocuments = [];

                foreach ($version->getDocsLanguages() as $language) {
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

            $this->projectGitSyncer->checkoutMaster($project->getRepositoryName());
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
