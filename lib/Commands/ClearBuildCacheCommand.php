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

class ClearBuildCacheCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'clear-build-cache';

    /** @var CacheClearer */
    private $cacheClearer;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $env;

    public function __construct(CacheClearer $cacheClearer, string $rootDir, string $env)
    {
        $this->cacheClearer = $cacheClearer;
        $this->rootDir      = $rootDir;
        $this->env          = $env;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Clear the build cache.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.',
                sprintf('%s/build-%s', $this->rootDir, $this->env)
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
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
