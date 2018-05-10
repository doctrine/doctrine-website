<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function realpath;
use function sprintf;

class ClearBuildCacheCommand extends ContainerAwareCommand
{
    protected function configure() : void
    {
        $this
            ->setName('clear:build-cache')
            ->setDescription('Clear the build cache.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $container = $this->getContainer();

        $rootDir  = realpath($container->getParameter('kernel.root_dir') . '/..');
        $env      = $container->getParameter('kernel.environment');
        $buildDir = $input->getArgument('build-dir');

        if (! $buildDir) {
            $buildDir = sprintf('%s/build-%s', $rootDir, $env);
        }

        $projectRepository = $container->get('doctrine.project.repository');

        // sculpin build directory
        $remove = [$buildDir];

        $projects = $projectRepository->findAll();

        foreach ($projects as $project) {
            // built rst docs
            $remove[] = sprintf(
                '%s/source/projects/%s',
                $rootDir,
                $project->getDocsSlug()
            );

            // built api docs
            $remove[] = sprintf(
                '%s/source/api/%s',
                $rootDir,
                $project->getSlug()
            );

            // api docs cache folder
            $remove[] = sprintf(
                '%s/projects/%s/cache',
                $rootDir,
                $project->getRepositoryName()
            );
        }

        $filesystem = $container->get('doctrine.filesystem');

        foreach ($remove as $path) {
            $output->writeln(sprintf('Removing <info>%s</info>', $path));

            $filesystem->remove($path);
        }
    }
}
