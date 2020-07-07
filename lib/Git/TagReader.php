<?php

declare(strict_types=1);

namespace Doctrine\Website\Git;

use DateTimeImmutable;
use Doctrine\Website\ProcessFactory;
use function explode;
use function preg_match_all;
use function sprintf;
use function str_replace;
use function usort;

class TagReader
{
    private const COMMAND = "cd %s && git tag -l --format='refname: %%(refname) creatordate: %%(creatordate)'";

    /** @var ProcessFactory */
    private $processFactory;

    public function __construct(ProcessFactory $processFactory)
    {
        $this->processFactory = $processFactory;
    }

    /**
     * @return Tag[]
     */
    public function getRepositoryTags(string $repositoryPath) : array
    {
        $lines = $this->getTagLines($repositoryPath);

        $tags = $this->createTagsFromLines($lines);

        usort($tags, static function (Tag $a, Tag $b) : int {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });

        return $tags;
    }

    /**
     * @return string[]
     */
    private function getTagLines(string $repositoryPath) : array
    {
        $command = sprintf(self::COMMAND, $repositoryPath);

        $process = $this->processFactory->run($command);

        return explode("\n", $process->getOutput());
    }

    /**
     * @param string[] $lines
     *
     * @return Tag[]
     */
    private function createTagsFromLines(array $lines) : array
    {
        $tags = [];

        foreach ($lines as $line) {
            $tag = $this->extractTagFromLine($line);

            if ($tag === null) {
                continue;
            }

            $tags[] = $tag;
        }

        return $tags;
    }

    private function extractTagFromLine(string $line) : ?Tag
    {
        preg_match_all('/refname: (.*) creatordate: (.*)/', $line, $matches);

        if (! isset($matches[1][0])) {
            return null;
        }

        $tagName = str_replace('refs/tags/', '', $matches[1][0]);

        $date = $matches[2][0];

        return new Tag($tagName, new DateTimeImmutable($date));
    }
}
