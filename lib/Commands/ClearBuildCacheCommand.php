<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Cache\CacheClearer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function sprintf;

final class ClearBuildCacheCommand extends Command
{
    public function __construct(
        private readonly CacheClearer $cacheClearer,
        private readonly string $rootDir,
        private readonly string $env,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('clear-build-cache')
            ->setDescription('Clear the build cache.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.',
                sprintf('%s/build-%s', $this->rootDir, $this->env),
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $buildDir = $input->getArgument('build-dir');
        assert(is_string($buildDir));

        $dirs = $this->cacheClearer->clear($buildDir);

        foreach ($dirs as $dir) {
            $output->writeln(sprintf('Removed <info>%s</info>', $dir));
        }

        return 0;
    }
}
