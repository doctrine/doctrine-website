<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Doctrine\Website\Docs\Preparer;
use Doctrine\Website\WebsiteBuilder;
use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildWebsiteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('build-website')
            ->setDescription('Build the Doctrine website.')
            ->addArgument(
                'build-dir',
                InputArgument::OPTIONAL,
                'The directory where the build repository is cloned.'
            )
            ->addOption(
                'publish',
                null,
                InputOption::VALUE_NONE,
                'Publish the build to GitHub Pages.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $env = $container->getParameter('kernel.environment');

        $publish = $input->getOption('publish');

        if ($publish && !in_array($env, WebsiteBuilder::PUBLISHABLE_ENVS)) {
            throw new InvalidArgumentException(sprintf('You cannot publish the "%s" environment.', $env));
        }

        $buildDir = $input->getArgument('build-dir');

        if (!$buildDir) {
            $buildDir = sprintf('/data/doctrine-website-sculpin-build-%s', $env);
        }

        if (!is_dir($buildDir)) {
            throw new InvalidArgumentException(sprintf('The build directory %s does not exist.', $buildDir));
        }

        $buildWebsite = $container->get('doctrine.website_builder');

        $buildWebsite->build($output, $buildDir, $env, $publish);
    }
}
