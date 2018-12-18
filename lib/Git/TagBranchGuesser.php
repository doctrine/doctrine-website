<?php

declare(strict_types=1);

namespace Doctrine\Website\Git;

use Doctrine\Website\ProcessFactory;
use function array_map;
use function explode;
use function ltrim;
use function preg_match_all;
use function sprintf;

class TagBranchGuesser
{
    private const COMMAND = 'cd %s && git branch -a';

    /** @var ProcessFactory */
    private $processFactory;

    public function __construct(ProcessFactory $processFactory)
    {
        $this->processFactory = $processFactory;
    }

    public function guessTagBranchName(string $repositoryPath, Tag $tag) : ?string
    {
        $command = sprintf(self::COMMAND, $repositoryPath);

        $process = $this->processFactory->run($command);

        $output = $process->getOutput();

        $lines = array_map('trim', explode("\n", $output));

        $tagBranchName = $this->generateTagBranchSlug($tag);

        $guesses = [
            $tagBranchName,
            sprintf('%s.x', $tagBranchName),
        ];

        foreach ($guesses as $branchName) {
            foreach ($lines as $line) {
                if ($line === 'remotes/origin/' . $branchName) {
                    return $branchName;
                }
            }
        }

        return null;
    }

    public function generateTagBranchSlug(Tag $tag) : ?string
    {
        $versionSlug = ltrim($tag->getName(), 'v');

        preg_match_all('/([0-9]+).([0-9]+)(.*)/', $versionSlug, $matches);

        if (! isset($matches[1][0])) {
            return null;
        }

        return $matches[1][0] . '.' . $matches[2][0];
    }
}
