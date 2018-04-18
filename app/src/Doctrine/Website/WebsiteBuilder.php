<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Dflydev\DotAccessConfiguration\Configuration;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Projects\ProjectVersion;
use Symfony\Component\Console\Output\OutputInterface;
use function chdir;
use function file_exists;
use function file_put_contents;
use function in_array;
use function is_dir;
use function realpath;
use function sprintf;
use function str_replace;
use function symlink;
use function unlink;

class WebsiteBuilder
{
    public const URL_LOCAL        = 'lcl.doctrine-project.org';
    public const URL_STAGING      = 'staging.doctrine-project.org';
    public const URL_PRODUCTION   = 'www.doctrine-project.org';
    public const PUBLISHABLE_ENVS = ['prod', 'staging'];

    /** @var ProcessFactory */
    private $processFactory;

    /** @var Configuration */
    private $sculpinConfig;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var string */
    private $kernelRootDir;

    public function __construct(
        ProcessFactory $processFactory,
        Configuration $sculpinConfig,
        ProjectRepository $projectRepository,
        string $kernelRootDir
    ) {
        $this->processFactory    = $processFactory;
        $this->sculpinConfig     = $sculpinConfig;
        $this->projectRepository = $projectRepository;
        $this->kernelRootDir     = $kernelRootDir;
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

        $rootDir = realpath($this->kernelRootDir . '/..');

        if ($publish) {
            $output->writeln(' - updating from git');

            $this->processFactory->run(sprintf('cd %s && git pull origin master', $buildDir));
        }

        $output->writeln(' - sculpin generate');

        $command = sprintf(
            'php -d memory_limit=1024M %s/vendor/bin/sculpin generate --env=%s',
            $rootDir,
            $env
        );

        $this->processFactory->run($command);

        $output->writeln(' - preparing build');

        $outputDir = sprintf('output_%s', $env);

        // cleanup the build directory
        $this->processFactory->run(sprintf('rm -rf %s/*', $buildDir));

        // copy the build to the build directory
        $this->processFactory->run(sprintf('mv %s/%s/* %s', $rootDir, $outputDir, $buildDir));

        // put the CNAME file back for publishable envs
        if (in_array($env, self::PUBLISHABLE_ENVS)) {
            $url   = $this->sculpinConfig->get('url');
            $cname = str_replace(['https://', 'http://'], '', $url);

            $this->filePutContents($buildDir . '/CNAME', $cname);
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

    protected function execute(string $command) : void
    {
        $this->processFactory->run($command);
    }

    private function createProjectVersionAliases(string $buildDir) : void
    {
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
