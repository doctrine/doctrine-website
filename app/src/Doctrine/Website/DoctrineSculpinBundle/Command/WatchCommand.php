<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('watch')
            ->setDescription('Watch for changes to the website source code and build.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the website is built',
                '/data/doctrine-website-sculpin-build-dev'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $buildDir = $input->getArgument('build-dir');

        if (!is_dir($buildDir)) {
            throw new InvalidArgumentException(sprintf('The build directory %s does not exist.', $buildDir));
        }

        $watcher = $this->getContainer()->get('doctrine.watcher');

        $watcher->watch($buildDir, $output);
    }
}
