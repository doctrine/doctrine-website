<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Doctrine\Website\Docs\Preparer;
use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildWebsiteCommand extends ContainerAwareCommand
{
    private const URL_LOCAL = 'lcl.doctrine-project.org';
    private const URL_STAGING = 'staging.doctrine-project.org';
    private const URL_PRODUCTION = 'new.doctrine-project.org';
    private const PUBLISHABLE_ENVS = ['prod', 'staging'];

    protected function configure()
    {
        $this
            ->setName('doctrine:build-website')
            ->setDescription('Build the Doctrine website.')
            ->addArgument('build-dir', null, InputArgument::REQUIRED, 'The directory where the doctrine-website-sculpin-build repository is cloned.')
            ->addOption('publish', null, InputOption::VALUE_NONE, 'Publish the build to GitHub Pages.')
            ->addOption('staging', null, InputOption::VALUE_NONE, 'Do a production build but for staging.doctrine-project.org')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $kernelRootDir = $container->getParameter('kernel.root_dir');
        $kernelEnv = $container->getParameter('kernel.environment');

        $publish = $input->getOption('publish');

        if ($publish && !in_array($kernelEnv, self::PUBLISHABLE_ENVS)) {
            throw new InvalidArgumentException(sprintf('You cannot publish the "%s" environment.', $kernelEnv));
        }

        $buildDir = $input->getArgument('build-dir');

        if (!$buildDir) {
            throw new InvalidArgumentException('You must pass the build-dir argument.');
        }

        if (!is_dir($buildDir)) {
            throw new InvalidArgumentException(sprintf('The build directory %s does not exist.', $buildDir));
        }

        $output->writeln(sprintf('Building website for <info>%s</info> environment.', $kernelEnv));

        $rootDir = realpath($kernelRootDir.'/..');

        $command = sprintf('%s/vendor/bin/sculpin generate --env=%s',
            $rootDir,
            $kernelEnv
        );

        passthru($command);

        $outputDir = sprintf('output_%s', $kernelEnv);

        // cleanup the build directory
        passthru(sprintf('rm -rf %s/*', $buildDir));

        // copy the build to the build directory
        passthru(sprintf('mv %s/%s/* %s', $rootDir, $outputDir, $buildDir));

        // put the CNAME file back
        $config = $this->getContainer()->get('sculpin.site_configuration');
        $url = $config->get('url');
        $cname = str_replace(['https://', 'http://'], '', $url);

        file_put_contents($buildDir.'/CNAME', $cname);

        if ($publish) {
            $output->writeln(sprintf('Publishing website for <info>%s</info> environment.', $kernelEnv));

            passthru(sprintf('cd %s && git add . && git commit -m"New version of Doctrine website" && git push origin master', $buildDir));
        }
    }
}
