<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Deployer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'deploy';

    /** @var Deployer */
    private $deployer;

    public function __construct(Deployer $deployer)
    {
        $this->deployer = $deployer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Deploy the Doctrine website.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deployer->deploy($output);

        return 0;
    }
}
