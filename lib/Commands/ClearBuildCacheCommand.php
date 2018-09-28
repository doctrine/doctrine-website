<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Repositories\ProjectRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use function array_filter;
use function glob;
use function sprintf;

class ClearBuildCacheCommand extends Command
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $env;

    public function __construct(ProjectRepository $projectRepository, Filesystem $filesystem, string $rootDir, string $env)
    {
        $this->projectRepository = $projectRepository;
        $this->filesystem        = $filesystem;
        $this->rootDir           = $rootDir;
        $this->env               = $env;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('clear-build-cache')
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

        // clear build directory
        $remove = [$buildDir];

        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            // built rst docs
            $remove[] = sprintf(
                '%s/source/projects/%s',
                $this->rootDir,
                $project->getDocsSlug()
            );

            // copied rst docs
            $remove[] = sprintf(
                '%s/docs/%s',
                $this->rootDir,
                $project->getDocsSlug()
            );

            // built api docs
            $remove[] = sprintf(
                '%s/source/api/%s',
                $this->rootDir,
                $project->getSlug()
            );

            // api docs cache folder
            $remove[] = sprintf(
                '%s/projects/%s/cache',
                $this->rootDir,
                $project->getRepositoryName()
            );
        }

        $cacheDirectories = array_filter(glob($this->rootDir . '/cache/*'), 'is_dir');

        foreach ($cacheDirectories as $cacheDirectory) {
            $remove[] = $cacheDirectory;
        }

        foreach ($remove as $path) {
            $output->writeln(sprintf('Removing <info>%s</info>', $path));

            $this->filesystem->remove($path);
        }

        return 0;
    }
}
