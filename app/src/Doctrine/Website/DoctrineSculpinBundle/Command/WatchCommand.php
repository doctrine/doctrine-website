<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function sprintf;

class WatchCommand extends ContainerAwareCommand
{
    protected function configure() : void
    {
        $this
            ->setName('watch')
            ->setDescription('Watch for changes to the website source code and build.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the website is built'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $container = $this->getContainer();

        $rootDir = $container->getParameter('kernel.root_dir');
        $env     = $container->getParameter('kernel.environment');

        $buildDir = $input->getArgument('build-dir');

        if (! $buildDir) {
            $buildDir = sprintf('%s/../build-%s', $rootDir, $env);
        }

        $watcher = $this->getContainer()->get('doctrine.watcher');

        $watcher->watch($buildDir, $output);
    }
}
