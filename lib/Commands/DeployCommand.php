<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Deployer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    /** @var Deployer */
    private $deployer;

    public function __construct(Deployer $deployer)
    {
        parent::__construct();

        $this->deployer = $deployer;
    }

    protected function configure() : void
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy the Doctrine website.')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The environment.'
            )
        ;
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->deployer->deploy($output);
    }
}
