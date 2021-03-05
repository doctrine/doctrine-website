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

class BuildAllCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'build-all';

    /** @var string */
    private $rootDir;

    /** @var string */
    private $env;

    public function __construct(
        string $rootDir,
        string $env
    ) {
        $this->rootDir = $rootDir;
        $this->env     = $env;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Build all website components.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.',
                sprintf('%s/build-%s', $this->rootDir, $this->env)
            )
            ->addOption(
                'publish',
                null,
                InputOption::VALUE_NONE,
                'Publish the build to GitHub Pages.'
            )
            ->addOption(
                'clear-build-cache',
                null,
                InputOption::VALUE_NONE,
                'Clear the build cache before building everything.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $buildDocsArgs = $this->env === 'prod' ? ['--search' => null] : [];
        $commands      = [
            'sync-repositories' => [],
            'build-website-data' => [],
            'build-docs' => $buildDocsArgs,
            'build-website' => [
                'build-dir' => $input->getArgument('build-dir'),
                '--publish' => $input->getOption('publish'),
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

    /**
     * @param mixed[] $arguments
     */
    private function runCommand(string $command, array $arguments): int
    {
        $input = new ArrayInput(array_merge(['command' => $command], $arguments));

        return $this->getApplication()->find($command)
            ->run($input, new ConsoleOutput());
    }
}
