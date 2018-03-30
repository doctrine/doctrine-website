<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Doctrine\Website\Docs\Preparer;
use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildWebsiteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('doctrine:build-website')
            ->setDescription('Build the Doctrine website.')
            ->addArgument('build-dir', null, InputArgument::REQUIRED, 'The directory where the doctrine-website-sculpin-build repository is cloned.')
            ->addOption('publish', null, InputOption::VALUE_NONE, 'Publish the build to GitHub Pages.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $buildDir = $input->getArgument('build-dir');

        if (!$buildDir) {
            throw new InvalidArgumentException('You must pass the build-dir argument.');
        }

        if (!is_dir($buildDir)) {
            throw new InvalidArgumentException(sprintf('The build directory %s does not exist.', $buildDir));
        }

        $container = $this->getContainer();

        $kernelRootDir = $container->getParameter('kernel.root_dir');
        $kernelEnv = $container->getParameter('kernel.environment');

        $rootDir = realpath($kernelRootDir.'/..');

        $command = sprintf('%s/vendor/bin/sculpin generate --env=%s', $rootDir, $kernelEnv);

        passthru($command);

        $outputDir = $kernelEnv === 'prod' ? 'output_prod' : 'output_dev';

        // cleanup the build directory
        passthru(sprintf('rm -rf %s/*', $buildDir));

        // copy the build to the build directory
        passthru(sprintf('mv %s/%s/* %s', $rootDir, $outputDir, $buildDir));

        // put the CNAME back in place when building prod
        if ($kernelEnv === 'prod') {
            file_put_contents($buildDir.'/CNAME', 'new.doctrine-project.org');

            if ($input->getOption('publish')) {
                passthru(sprintf('cd %s && git add . && git commit -m"New version of Doctrine website" && git push origin master', $buildDir));
            }
        }
    }
}
