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
    /** @var string */
    protected static $defaultName = 'build-website-data';

    /** @var ProjectDataBuilder */
    private $projectDataBuilder;

    /** @var ProjectContributorDataBuilder */
    private $projectContributorDataBuilder;

    /** @var ContributorDataBuilder */
    private $contributorDataBuilder;

    /** @var BlogPostDataBuilder */
    private $blogPostDataBuilder;

    /** @var WebsiteDataWriter */
    private $dataWriter;

    public function __construct(
        ProjectDataBuilder $projectDataBuilder,
        ProjectContributorDataBuilder $projectContributorDataBuilder,
        ContributorDataBuilder $contributorDataBuilder,
        BlogPostDataBuilder $blogPostDataBuilder,
        WebsiteDataWriter $dataWriter
    ) {
        $this->projectDataBuilder            = $projectDataBuilder;
        $this->projectContributorDataBuilder = $projectContributorDataBuilder;
        $this->contributorDataBuilder        = $contributorDataBuilder;
        $this->blogPostDataBuilder           = $blogPostDataBuilder;
        $this->dataWriter                    = $dataWriter;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Build the Doctrine website data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
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
