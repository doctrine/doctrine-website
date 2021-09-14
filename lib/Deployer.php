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

    /** @var string */
    private $rootDir;

    public function __construct(
        ProcessFactory $processFactory,
        string $env,
        string $rootDir
    ) {
        $this->processFactory = $processFactory;
        $this->env            = $env;
        $this->rootDir        = $rootDir;
    }

    public function deploy(OutputInterface $output): void
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
            'cd %s && git fetch && git checkout %s && git pull origin %s && php composer.phar install --no-dev && yarn install',
            $this->rootDir,
            $deployRef,
            $deployRef
        );

        $this->processFactory->run($updateCommand, static function ($type, $buffer) use ($output): void {
            $output->write($buffer);
        });

        // execute migrations, build the website and publish it.
        $deployCommand = sprintf(
            'cd %s && ./bin/console migrations:migrate --no-interaction --env=%s && ./bin/console build-all %s --env=%s --publish',
            $this->rootDir,
            $this->env,
            $this->rootDir,
            $this->env
        );

        $this->processFactory->run($deployCommand, static function ($type, $buffer) use ($output): void {
            $output->write($buffer);
        });
    }

    private function startDeploy(OutputInterface $output): void
    {
        $command = sprintf(
            'cp %s/deploy-%s %s/last-deploy-%s',
            $this->rootDir,
            $this->env,
            $this->rootDir,
            $this->env
        );

        $this->processFactory->run($command, static function ($type, $buffer) use ($output): void {
            $output->write($buffer);
        });
    }

    private function getDeploy(): string
    {
        return $this->getFileContents(sprintf(
            '%s/deploy-%s',
            $this->rootDir,
            $this->env
        ));
    }

    private function getLastDeploy(): string
    {
        return $this->getFileContents(sprintf(
            '%s/last-deploy-%s',
            $this->rootDir,
            $this->env
        ));
    }

    private function getFileContents(string $file): string
    {
        if (! file_exists($file)) {
            return '';
        }

        $contents = file_get_contents($file);
        assert(is_string($contents));

        return trim($contents);
    }
}
