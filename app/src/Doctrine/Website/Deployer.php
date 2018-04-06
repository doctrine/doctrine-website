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
    private $projectsPath;

    /** @var string */
    private $env;

    public function __construct(
        ProcessFactory $processFactory,
        string $projectsPath,
        string $env)
    {
        $this->processFactory = $processFactory;
        $this->projectsPath = $projectsPath;
        $this->env = $env;
    }

    public function deploy(OutputInterface $output)
    {
        if (!in_array($this->env, self::ENVS)) {
            throw new InvalidArgumentException(
                sprintf('Cannot deploy the %s environment.', $this->env)
            );
        }

        $deploy = $this->getDeploy();
        $lastDeploy = $this->getLastDeploy();

        if ($deploy === $lastDeploy) {
            $output->writeln('Nothing has changed. No need to deploy!');

            return;
        }

        $deployRef = $this->env === 'prod' ? 'master' : $deploy;

        $command = sprintf("cd /data/doctrine-website-%s && git fetch && git checkout %s && git pull origin %s && ./doctrine build-docs --api && ./doctrine build-website /data/doctrine-website-build-%s --env=%s --publish",
            $this->env,
            $deployRef,
            $deployRef,
            $this->env,
            $this->env,
            $this->env,
            $this->env,
            $this->env,
            $this->env
        );

        $output->writeln(sprintf('Deploying website for <info>%s</info> environment.', $this->env));


        try {
            $this->processFactory->run($command, function($type, $buffer) use ($output) {
                $output->write($buffer);
            });

            $this->finishDeploy($output);

        } catch (ProcessFailedException $e) {
            $this->finishDeploy($output);

            throw $e;
        }
    }

    protected function finishDeploy(OutputInterface $output)
    {
        $command = sprintf('cp /data/doctrine-website-%s/deploy-%s /data/doctrine-website-%s/last-deploy-%s',
            $this->env, $this->env, $this->env, $this->env
        );

        $this->processFactory->run($command, function($type, $buffer) use ($output) {
            $output->write($buffer);
        });
    }

    protected function getDeploy() : string
    {
        return $this->getFileContents(sprintf('/data/doctrine-website-%s/deploy-%s',
            $this->env, $this->env
        ));
    }

    protected function getLastDeploy() : string
    {
        return $this->getFileContents(sprintf('/data/doctrine-website-%s/last-deploy-%s',
            $this->env, $this->env
        ));
    }

    private function getFileContents(string $file) : string
    {
        if (!file_exists($file)) {
            return '';
        }

        return trim(file_get_contents($file));
    }
}
