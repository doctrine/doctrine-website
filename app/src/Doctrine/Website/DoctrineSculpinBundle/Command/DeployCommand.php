<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends ContainerAwareCommand
{
    protected function configure() : void
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy the Doctrine website.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $container = $this->getContainer();

        $deployer = $container->get('doctrine.deployer');

        $deployer->deploy($output);
    }
}
