<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;
use function array_unshift;
use function assert;
use function is_array;
use function is_bool;
use function is_string;
use function sprintf;

final class BuildAllCommand extends Command
{
    public function __construct(
        private readonly string $rootDir,
        private readonly string $env,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('build-all')
            ->setDescription('Build all website components.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.',
                sprintf('%s/build-%s', $this->rootDir, $this->env),
            )
            ->addOption(
                'clear-build-cache',
                null,
                InputOption::VALUE_NONE,
                'Clear the build cache before building everything.',
            )
            ->addOption(
                'search',
                null,
                InputOption::VALUE_NONE,
                'Build the search indexes.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $buildSearchIndexes = $input->getOption('search');
        assert(is_bool($buildSearchIndexes));

        $buildDocsArgs = $buildSearchIndexes ? ['--search' => null] : [];
        $commands      = [
            'sync-repositories' => [],
            'build-database' => [],
            'build-docs' => $buildDocsArgs,
            'build-website' => [
                'build-dir' => $input->getArgument('build-dir'),
            ],
        ];

        $clearBuildCache = $input->getOption('clear-build-cache');
        assert(is_bool($clearBuildCache));

        if ($clearBuildCache) {
            array_unshift($commands, 'clear-build-cache');
        }

        foreach ($commands as $command => $arguments) {
            assert(is_string($command));
            assert(is_array($arguments));

            $output->writeln(sprintf('Executing <info>./doctrine %s</info>', $command));

            if ($this->runCommand($command, $arguments) === 1) {
                $output->writeln(sprintf('Failed running command "%s".', $command));

                return 1;
            }
        }

        return 0;
    }

    /** @param mixed[] $arguments */
    private function runCommand(string $command, array $arguments): int
    {
        $input = new ArrayInput(array_merge(['command' => $command], $arguments));

        $application = $this->getApplication();

        assert($application !== null);

        return $application->find($command)->run($input, new ConsoleOutput());
    }
}
