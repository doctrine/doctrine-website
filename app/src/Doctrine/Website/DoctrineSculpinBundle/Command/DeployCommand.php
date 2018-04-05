<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy the Doctrine website.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $deployer = $container->get('doctrine.deployer');

        $deployer->deploy($output);
    }
}
