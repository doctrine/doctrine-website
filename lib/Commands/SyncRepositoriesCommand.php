<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Projects\ProjectDataRepository;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class SyncRepositoriesCommand extends Command
{
    public function __construct(
        private readonly ProjectDataRepository $projectDataRepository,
        private readonly ProjectGitSyncer $projectGitSyncer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('sync-repositories')
            ->setDescription('Initialize or update all project repositories.')
            ->addOption(
                'project',
                null,
                InputOption::VALUE_REQUIRED,
                'The project to build the docs for.',
                '',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project         = $input->getOption('project');
        $repositoryNames = $project === '' ? $this->projectDataRepository->getProjectRepositoryNames() : [$project];

        foreach ($repositoryNames as $repositoryName) {
            if ($this->projectGitSyncer->isRepositoryInitialized($repositoryName)) {
                $output->writeln(sprintf('Updating <info>%s</info>', $repositoryName));

                $this->projectGitSyncer->syncRepository($repositoryName);

                continue;
            }

            $output->writeln(sprintf('Initializing <info>%s</info>', $repositoryName));

            $this->projectGitSyncer->initRepository($repositoryName);
        }

        return 0;
    }
}
