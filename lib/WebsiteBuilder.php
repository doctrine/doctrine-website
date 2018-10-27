<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\Website\Builder\SourceFileBuilder;
use Doctrine\Website\Builder\SourceFileRepository;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
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

    /** @var SourceFileBuilder */
    private $sourceFileBuilder;

    /** @var string */
    private $webpackBuildPath;

    public function __construct(
        ProcessFactory $processFactory,
        ProjectRepository $projectRepository,
        Filesystem $filesystem,
        SourceFileRepository $sourceFileRepository,
        SourceFileBuilder $sourceFileBuilder,
        string $webpackBuildPath
    ) {
        $this->processFactory           = $processFactory;
        $this->projectRepository        = $projectRepository;
        $this->filesystem               = $filesystem;
        $this->sourceFileRepository     = $sourceFileRepository;
        $this->sourceFileBuilder        = $sourceFileBuilder;
        $this->webpackBuildPath   = $webpackBuildPath;
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

        $this->buildWebsite($buildDir, $isPublishableEnv);

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
    private function buildWebsite(string $buildDir, bool $isPublishableEnv) : void
    {
        // cleanup the build directory
        $this->filesystem->remove(glob($buildDir . '/*'));

        // Move webpack assets into build directory
        $this->buildWebpackAssets($buildDir, $isPublishableEnv);

        foreach ($this->sourceFileRepository->getFiles($buildDir) as $file) {
            try {
                $this->sourceFileBuilder->buildFile($file, $buildDir);
            } catch (Throwable $e) {
                throw new RuntimeException(sprintf(
                    'Failed building file "%s" with error "%s',
                    $file->getWritePath(),
                    $e->getMessage() . "\n\n" . $e->getTraceAsString()
                ));
            }
        }
    }

    private function buildWebpackAssets(string $buildDir, bool $isPublishableEnv) : void
    {
        $this->filesystem->remove(glob($this->webpackBuildPath . '/*'));
        $this->processFactory->run(sprintf('cd %s && npm run %s',
            $buildDir, $isPublishableEnv ? 'build' : 'dev'));

        // Copy built assets if this is a publishable build
        if ($isPublishableEnv) {
            $this->filesystem->mirror($this->webpackBuildPath, $buildDir);
            return;
        }

        // Symlink files to allow files to auto update using webpack --watch
        $this->filesystem->mkdir($buildDir);
        $this->filesystem->symlink($this->webpackBuildPath . '/css', $buildDir . '/css', true);
        $this->filesystem->symlink($this->webpackBuildPath . '/js', $buildDir . '/js', true);
    }

    private function createProjectVersionAliases(string $buildDir) : void
    {
        /** @var Project[] $projects */
        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            foreach ($project->getVersions() as $version) {
                foreach ($version->getAliases() as $alias) {
                    $this->createApiDocsProjectVersionAlias(
                        $buildDir,
                        $project,
                        $version,
                        $alias
                    );

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

    private function createApiDocsProjectVersionAlias(
        string $buildDir,
        Project $project,
        ProjectVersion $version,
        string $alias
    ) : void {
        $dir = sprintf(
            '%s/api/%s',
            $buildDir,
            $project->getSlug()
        );

        $this->createVersionAlias($dir, $version, $alias);
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
