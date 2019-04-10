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
use function file_exists;
use function file_put_contents;
use function glob;
use function in_array;
use function is_dir;
use function sprintf;
use function symlink;
use function unlink;

class WebsiteBuilder
{
    public const PUBLISHABLE_ENVS = ['prod', 'staging'];

    private const URL_PRODUCTION = 'www.doctrine-project.org';
    private const URL_STAGING    = 'staging.doctrine-project.org';

    private const PUBLISHABLE_ENV_URLS = [
        'prod' => self::URL_PRODUCTION,
        'staging' => self::URL_STAGING,
    ];

    /** @var ProcessFactory */
    private $processFactory;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var Filesystem */
    private $filesystem;

    /** @var SourceFileRepository */
    private $sourceFileRepository;

    /** @var SourceFilesBuilder */
    private $sourceFilesBuilder;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $webpackBuildDir;

    public function __construct(
        ProcessFactory $processFactory,
        ProjectRepository $projectRepository,
        Filesystem $filesystem,
        SourceFileRepository $sourceFileRepository,
        SourceFilesBuilder $sourceFilesBuilder,
        string $rootDir,
        string $webpackBuildDir
    ) {
        $this->processFactory       = $processFactory;
        $this->projectRepository    = $projectRepository;
        $this->filesystem           = $filesystem;
        $this->sourceFileRepository = $sourceFileRepository;
        $this->sourceFilesBuilder   = $sourceFilesBuilder;
        $this->rootDir              = $rootDir;
        $this->webpackBuildDir      = $webpackBuildDir;
    }

    public function build(
        OutputInterface $output,
        string $buildDir,
        string $env,
        bool $publish
    ) : void {
        $output->writeln(sprintf(
            'Building Doctrine website for <info>%s</info> environment at <info>%s</info>.',
            $env,
            $buildDir
        ));

        $isPublishableEnv = in_array($env, self::PUBLISHABLE_ENVS, true);
        if ($publish) {
            $output->writeln(' - updating from git');

            $this->processFactory->run(sprintf('cd %s && git pull origin master', $buildDir));
        }

        $output->writeln(' - building website');

        $this->buildWebsite($output, $buildDir, $isPublishableEnv);

        // put the CNAME file back for publishable envs
        if ($isPublishableEnv) {
            $this->filePutContents($buildDir . '/CNAME', self::PUBLISHABLE_ENV_URLS[$env]);
        }

        $this->createProjectVersionAliases($buildDir);

        if ($publish) {
            $output->writeln(' - publishing build');

            $this->processFactory->run(sprintf('cd %s && git pull origin master && git add . --all && git commit -m"New version of Doctrine website" && git push origin master', $buildDir));
        }

        $output->writeln(' - done');
    }

    protected function filePutContents(string $path, string $contents) : void
    {
        file_put_contents($path, $contents);
    }

    /**
     * @throws RuntimeException
     */
    private function buildWebsite(OutputInterface $output, string $buildDir, bool $isPublishableEnv) : void
    {
        // cleanup the build directory
        $this->filesystem->remove(glob($buildDir . '/*'));

        // Move webpack assets into build directory
        $this->buildWebpackAssets($output, $buildDir, $isPublishableEnv);

        $this->sourceFilesBuilder->buildSourceFiles(
            $this->sourceFileRepository->getSourceFiles($buildDir)
        );
    }

    private function buildWebpackAssets(OutputInterface $output, string $buildDir, bool $isPublishableEnv) : void
    {
        $output->writeln(sprintf(' - running npm run %s ', $isPublishableEnv ? 'build' : 'dev'));
        $this->filesystem->remove(glob($this->webpackBuildDir . '/*'));
        $process = $this->processFactory->run(sprintf(
            'cd %s && npm run %s',
            $this->rootDir,
            $isPublishableEnv ? 'build' : 'dev'
        ));
        $output->write($process->getOutput());

        // Copy built assets if this is a publishable build
        if ($isPublishableEnv) {
            $this->filesystem->mirror($this->webpackBuildDir, $buildDir . '/frontend');
            return;
        }

        // Symlink files to allow files to auto update using webpack --watch
        $this->filesystem->mkdir($buildDir);
        $this->filesystem->symlink($this->webpackBuildDir, $buildDir . '/frontend', true);
    }

    private function createProjectVersionAliases(string $buildDir) : void
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
                        $alias
                    );
                }
            }
        }
    }

    private function createDocsProjectVersionAlias(
        string $buildDir,
        Project $project,
        ProjectVersion $version,
        string $alias
    ) : void {
        $dir = sprintf(
            '%s/projects/%s/en',
            $buildDir,
            $project->getDocsSlug()
        );

        $this->createVersionAlias($dir, $version, $alias);
    }

    private function createVersionAlias(string $dir, ProjectVersion $version, string $alias) : void
    {
        if (! is_dir($dir)) {
            return;
        }

        chdir($dir);

        if (file_exists($alias)) {
            unlink($alias);
        }

        if (! file_exists($version->getSlug())) {
            return;
        }

        symlink($version->getSlug(), $alias);
    }
}
