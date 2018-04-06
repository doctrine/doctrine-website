<?php

namespace Doctrine\Website\Docs;

use Doctrine\Website\Docs\APIBuilder;
use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        SearchIndexer $searchIndexer)
    {
        $this->projectRepository = $projectRepository;
        $this->projectGitSyncer = $projectGitSyncer;
        $this->apiBuilder = $apiBuilder;
        $this->rstBuilder = $rstBuilder;
        $this->searchIndexer = $searchIndexer;
    }

    public function  build(
        OutputInterface $output,
        string $projectToBuild,
        string $versionToBuilder,
        bool $buildApiDocs,
        bool $buildSearchIndexes)
    {
        if ($buildSearchIndexes) {
            $this->searchIndexer->initSearchIndex();
        }

        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            if ($projectToBuild && $project->getSlug() !== $projectToBuild) {
                continue;
            }

            foreach ($project->getVersions() as $version) {
                if ($versionToBuilder && $version->getSlug() !== $versionToBuilder) {
                    continue;
                }

                $output->writeln(sprintf('<info>%s</info> (<comment>%s</comment>)',
                    $project->getSlug(), $version->getSlug()
                ));

                $output->writeln(' - syncing git');

                $this->projectGitSyncer->sync($project, $version);

                if ($buildApiDocs) {
                    $output->writeln(' - building api docs');

                    try {
                        $this->apiBuilder->buildAPIDocs($project, $version);
                    } catch (ProcessFailedException $e) {
                        $output->writeln(' - <error>building api docs failed</error>');
                    }
                }

                if (!$this->rstBuilder->projectHasDocs($project)) {
                    $output->writeln(' - <warning>no docs found</warning>');

                    continue;
                }

                $output->writeln(' - building rst docs');

                $this->rstBuilder->buildRSTDocs($project, $version);

                if ($buildSearchIndexes) {
                    $output->writeln(' - building search indexes');

                    $this->searchIndexer->buildSearchIndexes($project, $version);
                }
            }
        }
    }
}
