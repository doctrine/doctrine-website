<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\Website\Release\AnnounceRelease;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function assert;
use function is_string;
use function sprintf;

final class AnnounceReleaseCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'announce-release';

    /** @var AnnounceRelease */
    private $announceRelease;

    public function __construct(AnnounceRelease $announceRelease)
    {
        $this->announceRelease = $announceRelease;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Announce a release on Twitter, Slack, etc.')
            ->addArgument(
                'project',
                InputArgument::OPTIONAL,
                'The project slug.'
            )
            ->addArgument(
                'tag',
                InputArgument::OPTIONAL,
                'The tag slug.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectSlug = $input->getArgument('project');
        assert(is_string($projectSlug));

        $tagSlug = $input->getArgument('tag');
        assert(is_string($tagSlug));

        try {
            $this->announceRelease->__invoke($projectSlug, $tagSlug);
        } catch (Throwable $e) {
            $output->writeln(sprintf(
                '<error>Failed to announce release! Failed with error: %s</error>',
                $e->getMessage()
            ));

            return 1;
        }

        $output->writeln('Successfully announced release!');

        return 0;
    }
}
