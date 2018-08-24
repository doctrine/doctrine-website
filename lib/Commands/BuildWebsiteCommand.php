<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\WebsiteBuilder;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function in_array;
use function ini_set;
use function is_dir;
use function mkdir;
use function realpath;
use function sprintf;

class BuildWebsiteCommand extends Command
{
    /** @var WebsiteBuilder */
    private $websiteBuilder;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $env;

    public function __construct(
        WebsiteBuilder $websiteBuilder,
        string $rootDir,
        string $env
    ) {
        parent::__construct();

        $this->websiteBuilder = $websiteBuilder;
        $this->rootDir        = $rootDir;
        $this->env            = $env;
    }

    protected function configure() : void
    {
        $this
            ->setName('build-website')
            ->setDescription('Build the Doctrine website.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.'
            )
            ->addOption(
                'publish',
                null,
                InputOption::VALUE_NONE,
                'Publish the build to GitHub Pages.'
            )
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The environment.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        ini_set('memory_limit', '1024M');

        $publish = (bool) $input->getOption('publish');

        if ($publish && ! in_array($this->env, WebsiteBuilder::PUBLISHABLE_ENVS, true)) {
            throw new InvalidArgumentException(sprintf('You cannot publish the "%s" environment.', $this->env));
        }

        $buildDir = $input->getArgument('build-dir');

        if ($buildDir === null) {
            $buildDir = sprintf('%s/build-%s', $this->rootDir, $this->env);
        }

        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0777, true);
        }

        $buildDir = realpath($buildDir);

        $this->websiteBuilder->build($output, $buildDir, $this->env, $publish);
    }
}