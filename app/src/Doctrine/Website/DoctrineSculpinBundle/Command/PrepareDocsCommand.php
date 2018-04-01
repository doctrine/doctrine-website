<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Doctrine\Website\Docs\Preparer;
use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareDocsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('doctrine:prepare-docs')
            ->setDescription('Prepare docs.')
            ->addArgument('dir', null, InputArgument::REQUIRED, 'The directory where the documentation repositories are cloned.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $sculpinRstKernel = $container->get('sculpin_rst.kernel.sculpin');

        $kernelRootDir = $container->getParameter('kernel.root_dir');

        /** Clone Doctrine repositories to this path */
        $projectsPath = $input->getArgument('dir');

        if (!$projectsPath) {
            throw new InvalidArgumentException('You must pass the --dir option to configure ');
        }

        if (!is_dir($projectsPath)) {
            mkdir($projectsPath, 0777, true);
        }

        /** Path to Doctrine website Sculpin source files */
        $sculpinSourcePath = realpath($kernelRootDir.'/../source');

        $projects = $container->get('doctrine.project.repository')->findAll();

        foreach ($projects as $project) {
            if (!$project->hasDocs()) {
                continue;
            }

            foreach ($project->getVersions() as $version) {
                $preparer = new Preparer(
                    $sculpinSourcePath,
                    $projectsPath,
                    $sculpinRstKernel,
                    $project,
                    $version
                );

                $preparer->prepareGit($output);

                if (!$preparer->versionHasDocs($project, $version)) {
                    continue;
                }

                $output->writeln(sprintf('Building docs for <info>%s</info> version <info>%s</info>',
                    $project->getSlug(),
                    $version->getSlug()
                ));

                $preparer->prepare($output);
            }
        }
    }
}
