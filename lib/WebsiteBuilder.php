<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileRepository;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFilesBuilder;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function chdir;
use function file_put_contents;
use function getcwd;
use function glob;
use function in_array;
use function is_dir;
use function sprintf;

class WebsiteBuilder
{
    public const PUBLISHABLE_ENVS = [Application::ENV_PROD, Application::ENV_STAGING];

    private const URL_PRODUCTION = 'www.doctrine-project.org';
    private const URL_STAGING    = 'staging.doctrine-project.org';

    private const PUBLISHABLE_ENV_URLS = [
        Application::ENV_PROD    => self::URL_PRODUCTION,
        Application::ENV_STAGING => self::URL_STAGING,
    ];

    /** @param ProjectRepository<Project> $projectRepository */
    public function __construct(
        private ProcessFactory $processFactory,
        private ProjectRepository $projectRepository,
        private Filesystem $filesystem,
        private SourceFileRepository $sourceFileRepository,
        private SourceFilesBuilder $sourceFilesBuilder,
        private string $rootDir,
        private string $cacheDir,
        private string $webpackBuildDir,
    ) {
    }

    public function build(
        OutputInterface $output,
        string $buildDir,
        string $env,
    ): void {
        $output->writeln(sprintf(
            'Building Doctrine website for <info>%s</info> environment at <info>%s</info>.',
            $env,
            $buildDir,
        ));

        $isPublishableEnv = in_array($env, self::PUBLISHABLE_ENVS, true);

        $output->writeln(' - building website');

        $this->buildWebsite($output, $buildDir, $isPublishableEnv);

        // put the CNAME file back for publishable envs
        if ($isPublishableEnv) {
            $this->filePutContents($buildDir . '/CNAME', self::PUBLISHABLE_ENV_URLS[$env]);
        }

        $this->createProjectVersionAliases($buildDir);

        $this->copyWebsiteBuildData($output, $buildDir);

        $output->writeln(' - done');
    }

    protected function filePutContents(string $path, string $contents): void
    {
        file_put_contents($path, $contents);
    }

    /** @throws RuntimeException */
    private function buildWebsite(OutputInterface $output, string $buildDir, bool $isPublishableEnv): void
    {
        $output->writeln(sprintf(' - clearing build directory <info>%s</info>', $buildDir));

        // cleanup the build directory
        $this->filesystem->remove((array) glob($buildDir . '/*'));

        // Move webpack assets into build directory
        $this->buildWebpackAssets($output, $buildDir, $isPublishableEnv);

        $output->writeln(' - calculating source files to build');

        $sourceFiles = $this->sourceFileRepository->getSourceFiles($buildDir);

        $output->writeln(sprintf(' - building source files to <info>%s</info>', $buildDir));

        $this->sourceFilesBuilder->buildSourceFiles($sourceFiles);
    }

    private function buildWebpackAssets(OutputInterface $output, string $buildDir, bool $isPublishableEnv): void
    {
        $output->writeln(sprintf(' - running <info>npm run %s</info> ', $isPublishableEnv ? 'build' : 'dev'));

        $this->filesystem->remove((array) glob($this->webpackBuildDir . '/*'));

        $process = $this->processFactory->run(sprintf(
            'cd %s && npm run %s',
            $this->rootDir,
            $isPublishableEnv ? 'build' : 'dev',
        ));

        if ($output->isVerbose()) {
            $output->write($process->getOutput());
        }

        // Copy built assets if this is a publishable build
        if ($isPublishableEnv) {
            $this->filesystem->mirror($this->webpackBuildDir, $buildDir . '/frontend');

            return;
        }

        // Symlink files to allow files to auto update using webpack --watch
        $this->filesystem->mkdir($buildDir);
        $this->filesystem->symlink($this->webpackBuildDir, $buildDir . '/frontend', true);
    }

    private function createProjectVersionAliases(string $buildDir): void
    {
        /** @var Project[] $projects */
        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            foreach ($project->getVersions() as $version) {
                foreach ($version->getAliases() as $alias) {
                    $this->createDocsProjectVersionAlias(
                        $buildDir,
                        $project,
                        $version,
                        $alias,
                    );
                }
            }
        }
    }

    private function copyWebsiteBuildData(OutputInterface $output, string $buildDir): void
    {
        $from = $this->cacheDir . '/data';
        $to   = $buildDir . '/website-data';

        $output->writeln(sprintf(
            ' - copying website build data from <info>%s</info> to <info>%s</info>.',
            $from,
            $to,
        ));

        $this->filesystem->mirror($from, $to);
    }

    private function createDocsProjectVersionAlias(
        string $buildDir,
        Project $project,
        ProjectVersion $version,
        string $alias,
    ): void {
        $dir = sprintf(
            '%s/projects/%s/en',
            $buildDir,
            $project->getDocsSlug(),
        );

        $this->createVersionAlias($dir, $version, $alias);
    }

    private function createVersionAlias(string $dir, ProjectVersion $version, string $alias): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $cwd = getcwd();

        chdir($dir);

        if ($this->filesystem->exists($alias)) {
            $this->filesystem->remove($alias);
        }

        if ($this->filesystem->exists($version->getSlug())) {
            $this->filesystem->symlink($version->getSlug(), $alias);
        }

        if ($cwd === false) {
            return;
        }

        chdir($cwd);
    }
}
