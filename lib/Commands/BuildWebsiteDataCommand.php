<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\DataBuilder\BlogPostDataBuilder;
use Doctrine\Website\DataBuilder\ContributorDataBuilder;
use Doctrine\Website\DataBuilder\ProjectContributorDataBuilder;
use Doctrine\Website\DataBuilder\ProjectDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class BuildWebsiteDataCommand extends Command
{
    protected static string|null $defaultName = 'build-website-data';

    public function __construct(
        private ProjectDataBuilder $projectDataBuilder,
        private ProjectContributorDataBuilder $projectContributorDataBuilder,
        private ContributorDataBuilder $contributorDataBuilder,
        private BlogPostDataBuilder $blogPostDataBuilder,
        private WebsiteDataWriter $dataWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Build the Doctrine website data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Building website data.');

        $dataBuilders = [
            $this->projectDataBuilder,
            $this->projectContributorDataBuilder,
            $this->contributorDataBuilder,
            $this->blogPostDataBuilder,
        ];

        foreach ($dataBuilders as $dataBuilder) {
            $output->writeln(sprintf('Building <info>%s</info> data.', $dataBuilder->getName()));

            $websiteData = $dataBuilder->build();

            $this->dataWriter->write($websiteData);
        }

        return 0;
    }
}
