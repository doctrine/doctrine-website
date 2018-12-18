<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\DataBuilder\WebsiteDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function ini_set;
use function sprintf;

class BuildWebsiteDataCommand extends Command
{
    /** @var WebsiteDataBuilder */
    private $websiteDataBuilder;

    /** @var WebsiteDataWriter */
    private $dataWriter;

    public function __construct(
        WebsiteDataBuilder $websiteDataBuilder,
        WebsiteDataWriter $dataWriter
    ) {
        $this->websiteDataBuilder = $websiteDataBuilder;
        $this->dataWriter         = $dataWriter;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('build-website-data')
            ->setDescription('Build the Doctrine website data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        ini_set('memory_limit', '1024M');

        $output->writeln('Building website data.');

        foreach ($this->websiteDataBuilder->build() as $websiteData) {
            $output->writeln(sprintf('Writing <info>%s</info> data.', $websiteData->getName()));

            $this->dataWriter->write($websiteData);
        }

        return 0;
    }
}
