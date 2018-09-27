<?php

declare(strict_types=1);

namespace Doctrine\Website;

use InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;
use function assert;
use function file_exists;
use function file_get_contents;
use function in_array;
use function is_string;
use function sprintf;
use function trim;

class Deployer
{
    public const ENV_PROD    = 'prod';
    public const ENV_STAGING = 'staging';
    public const ENVS        = [
        self::ENV_PROD,
        self::ENV_STAGING,
    ];

    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $env;

    public function __construct(
        ProcessFactory $processFactory,
        string $env
    ) {
        $this->processFactory = $processFactory;
        $this->env            = $env;
    }

    public function deploy(OutputInterface $output) : void
    {
        if (! in_array($this->env, self::ENVS, true)) {
            throw new InvalidArgumentException(
                sprintf('Cannot deploy the %s environment.', $this->env)
            );
        }

        $deploy     = $this->getDeploy();
        $lastDeploy = $this->getLastDeploy();

        if ($deploy === $lastDeploy) {
            $output->writeln('Nothing has changed. No need to deploy!');

            return;
        }

        $this->startDeploy($output);

        $deployRef = $this->env === 'prod' ? 'master' : $deploy;

        $output->writeln(sprintf('Deploying website for <info>%s</info> environment.', $this->env));

        // update the code from git and run composer install first
        $updateCommand = sprintf(
            'cd /data/doctrine-website-%s && git fetch && git checkout %s && git pull origin %s && php composer.phar install --no-dev',
            $this->env,
            $deployRef,
            $deployRef
        );

        $this->processFactory->run($updateCommand, static function ($type, $buffer) use ($output) : void {
            $output->write($buffer);
        });

        // build the docs, website and publish
        $deployCommand = sprintf(
            'cd /data/doctrine-website-%s && ./bin/console build-docs --api --sync-git && ./bin/console build-website /data/doctrine-website-build-%s --env=%s --publish',
            $this->env,
            $this->env,
            $this->env
        );

        $this->processFactory->run($deployCommand, static function ($type, $buffer) use ($output) : void {
            $output->write($buffer);
        });
    }

    protected function startDeploy(OutputInterface $output) : void
    {
        $command = sprintf(
            'cp /data/doctrine-website-%s/deploy-%s /data/doctrine-website-%s/last-deploy-%s',
            $this->env,
            $this->env,
            $this->env,
            $this->env
        );

        $this->processFactory->run($command, static function ($type, $buffer) use ($output) : void {
            $output->write($buffer);
        });
    }

    protected function getDeploy() : string
    {
        return $this->getFileContents(sprintf(
            '/data/doctrine-website-%s/deploy-%s',
            $this->env,
            $this->env
        ));
    }

    protected function getLastDeploy() : string
    {
        return $this->getFileContents(sprintf(
            '/data/doctrine-website-%s/last-deploy-%s',
            $this->env,
            $this->env
        ));
    }

    private function getFileContents(string $file) : string
    {
        if (! file_exists($file)) {
            return '';
        }

        $contents = file_get_contents($file);
        assert(is_string($contents));

        return trim($contents);
    }
}
