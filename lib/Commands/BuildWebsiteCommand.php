<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\WebsiteBuilder;
use Highlight\Highlighter;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use function array_map;
use function assert;
use function date;
use function in_array;
use function ini_set;
use function is_bool;
use function is_dir;
use function is_string;
use function mkdir;
use function realpath;
use function sprintf;
use function time;

class BuildWebsiteCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'build-website';

    private const WATCH_DIRS = [
        'config',
        'data',
        'lib',
        'source',
        'templates',
    ];

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
        $this->websiteBuilder = $websiteBuilder;
        $this->rootDir        = $rootDir;
        $this->env            = $env;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Build the Doctrine website.')
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
                'watch',
                null,
                InputOption::VALUE_NONE,
                'Watch for changes and build the website when changes are detected.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '2048M');
        $this->registerHighlighter();

        $publish = $input->getOption('publish');
        assert(is_bool($publish));

        if ($publish && ! in_array($this->env, WebsiteBuilder::PUBLISHABLE_ENVS, true)) {
            throw new InvalidArgumentException(sprintf('You cannot publish the "%s" environment.', $this->env));
        }

        $buildDir = $input->getArgument('build-dir');
        assert(is_string($buildDir));

        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0777, true);
        }

        $buildDir = realpath($buildDir);

        if ($buildDir === false) {
            throw new InvalidArgumentException(sprintf('Could not find build directory'));
        }

        $watch = $input->getOption('watch');
        assert(is_bool($watch));

        if ($watch) {
            $this->watch($output);
        } else {
            $this->websiteBuilder->build($output, $buildDir, $this->env, $publish);
        }

        return 0;
    }

    private function watch(OutputInterface $output): void
    {
        $lastWebsiteBuild = time();

        while (true) {
            $finder = $this->createWatchFinder($lastWebsiteBuild);

            if (! $finder->hasResults()) {
                continue;
            }

            $output->writeln('Found changes');

            $this->buildWebsiteSubProcess($output);

            $lastWebsiteBuild = time();
        }
    }

    private function createWatchFinder(int $lastWebsiteBuild): Finder
    {
        return (new Finder())
            ->in($this->getWatchDirs())
            ->date(sprintf('>= %s', date('Y-m-d H:i:s', $lastWebsiteBuild)));
    }

    /**
     * @return string[]
     */
    private function getWatchDirs(): array
    {
        return array_map(function (string $dir): string {
            return $this->rootDir . '/' . $dir;
        }, self::WATCH_DIRS);
    }

    private function buildWebsiteSubProcess(OutputInterface $output): void
    {
        (new Process(['bin/console', 'build-website'], $this->rootDir))
            ->setTty(true)
            ->mustRun(static function ($type, $buffer) use ($output): void {
                $output->write($buffer);
            });
    }

    private function registerHighlighter(): void
    {
        $phpHighlightPath = sprintf('%s/vendor/scrivo/highlight.php/Highlight/languages/php.json', $this->rootDir);
        Highlighter::registerLanguage('annotation', $phpHighlightPath);
        Highlighter::registerLanguage('attribute', $phpHighlightPath);
    }
}
