<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\DataBuilder\DataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class BuildWebsiteDataCommand extends Command
{
    /** @param iterable<DataBuilder> $dataBuilders */
    public function __construct(private readonly iterable $dataBuilders, private readonly WebsiteDataWriter $dataWriter)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('build-website-data')
            ->setDescription('Build the Doctrine website data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Building website data.');

        foreach ($this->dataBuilders as $dataBuilder) {
            $output->writeln(sprintf('Building <info>%s</info> data.', $dataBuilder->getName()));

            $websiteData = $dataBuilder->build();

            $this->dataWriter->write($websiteData);
        }

        return 0;
    }
}
