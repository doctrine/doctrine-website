<?php

namespace Doctrine\Website;

use InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Deployer
{
    const ENV_PROD = 'prod';
    const ENV_STAGING = 'staging';
    const ENVS = [
        self::ENV_PROD,
        self::ENV_STAGING
    ];

    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $env;

    public function __construct(ProcessFactory $processFactory, string $env)
    {
        $this->processFactory = $processFactory;
        $this->env = $env;
    }

    public function deploy(OutputInterface $output)
    {
        if (!in_array($this->env, self::ENVS)) {
            throw new InvalidArgumentException(
                sprintf('Cannot deploy the %s environment.', $this->env)
            );
        }

        $deploy = trim(@file_get_contents(sprintf('/data/doctrine/deploy-%s', $this->env)));
        $lastDeploy = trim(@file_get_contents(sprintf('/data/doctrine/last-deploy-%s', $this->env)));

        if ($deploy === $lastDeploy) {
            $output->writeln('Nothing has changed. No need to deploy!');

            return;
        }

        $deployRef = $this->env === 'prod' ? 'master' : $deploy;

        $command = sprintf("cd /data/doctrine-website-sculpin-%s && git fetch && git checkout %s && git pull origin %s && ./doctrine build-docs && ./doctrine build-website /data/doctrine-website-sculpin-build-%s --env=%s --publish && cp /data/doctrine/deploy-%s /data/doctrine/last-deploy-%s",
            $this->env,
            $deployRef,
            $deployRef,
            $this->env,
            $this->env,
            $this->env,
            $this->env
        );

        $output->writeln(sprintf('Deploying website for <info>%s</info> environment.', $this->env));

        $process = $this->processFactory->run($command, function($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
