<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Docs\BuildDocs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDocsCommand extends Command
{
    /** @var BuildDocs */
    private $buildDocs;

    public function __construct(BuildDocs $buildDocs)
    {
        $this->buildDocs = $buildDocs;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('build-docs')
            ->setDescription('Build the RST and API docs.')
            ->addOption(
                'project',
                null,
                InputOption::VALUE_REQUIRED,
                'The project to build the docs for.'
            )
            ->addOption(
                'v',
                null,
                InputOption::VALUE_REQUIRED,
                'The project version to build the docs for.'
            )
            ->addOption(
                'api',
                null,
                InputOption::VALUE_NONE,
                'Build the api documentation.'
            )
            ->addOption(
                'search',
                null,
                InputOption::VALUE_NONE,
                'Build the search indexes.'
            )
            ->addOption(
                'sync-git',
                null,
                InputOption::VALUE_NONE,
                'Sync git repositories before building.'
            )
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The environment.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $projectToBuild     = (string) $input->getOption('project');
        $versionToBuild     = (string) $input->getOption('v');
        $buildApiDocs       = (bool) $input->getOption('api');
        $buildSearchIndexes = (bool) $input->getOption('search');
        $syncGit            = (bool) $input->getOption('sync-git');

        $this->buildDocs->build(
            $output,
            $projectToBuild,
            $versionToBuild,
            $buildApiDocs,
            $buildSearchIndexes,
            $syncGit
        );

        return 0;
    }
}
