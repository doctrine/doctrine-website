<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDocsCommand extends ContainerAwareCommand
{
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $projectToBuild     = (string) $input->getOption('project');
        $versionToBuild     = (string) $input->getOption('v');
        $buildApiDocs       = (bool) $input->getOption('api');
        $buildSearchIndexes = (bool) $input->getOption('search');
        $syncGit            = (bool) $input->getOption('sync-git');

        $buildDocs = $this->getContainer()->get('doctrine.docs.build_docs');

        $buildDocs->build(
            $output,
            $projectToBuild,
            $versionToBuild,
            $buildApiDocs,
            $buildSearchIndexes,
            $syncGit
        );
    }
}
