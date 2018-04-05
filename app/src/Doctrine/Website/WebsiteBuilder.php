<?php

namespace Doctrine\Website;

use Dflydev\DotAccessConfiguration\Configuration;
use InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;

class WebsiteBuilder
{
    const URL_LOCAL = 'lcl.doctrine-project.org';
    const URL_STAGING = 'staging.doctrine-project.org';
    const URL_PRODUCTION = 'new.doctrine-project.org';
    const PUBLISHABLE_ENVS = ['prod', 'staging'];

    /** @var ProcessFactory */
    private $processFactory;

    /** @var Configuration */
    private $sculpinConfig;

    /** @var string */
    private $kernelRootDir;

    public function __construct(
        ProcessFactory $processFactory,
        Configuration $sculpinConfig,
        string $kernelRootDir)
    {
        $this->processFactory = $processFactory;
        $this->sculpinConfig = $sculpinConfig;
        $this->kernelRootDir = $kernelRootDir;
    }

    public function build(
        OutputInterface $output,
        string $buildDir,
        string $env,
        bool $publish)
    {
        $output->writeln(sprintf('Building Doctrine website for <info>%s</info> environment at <info>%s</info>.',
            $env,
            $buildDir
        ));

        $rootDir = realpath($this->kernelRootDir.'/..');

        if ($publish) {
            $output->writeln(' - updating from git');

            $this->execute(sprintf('cd %s && git pull origin master', $buildDir));
        }

        $output->writeln(' - sculpin generate');

        $command = sprintf('%s/vendor/bin/sculpin generate --env=%s',
            $rootDir,
            $env
        );

        $this->execute($command);

        $output->writeln(' - preparing build');

        $outputDir = sprintf('output_%s', $env);

        // cleanup the build directory
        $this->execute(sprintf('rm -rf %s/*', $buildDir));

        // copy the build to the build directory
        $this->execute(sprintf('mv %s/%s/* %s', $rootDir, $outputDir, $buildDir));

        // put the CNAME file back for publishable envs
        if (in_array($env, self::PUBLISHABLE_ENVS)) {
            $url = $this->sculpinConfig->get('url');
            $cname = str_replace(['https://', 'http://'], '', $url);

            file_put_contents($buildDir.'/CNAME', $cname);
        }

        if ($publish) {
            $output->writeln(' - publishing build');

            $this->execute(sprintf('cd %s && git pull origin master && git add . --all && git commit -m"New version of Doctrine website" && git push origin master', $buildDir));
        }

        $output->writeln(' - done');
    }

    private function execute(string $command)
    {
        $process = $this->processFactory->run($command);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
