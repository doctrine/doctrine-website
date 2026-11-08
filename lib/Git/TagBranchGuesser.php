<?php

declare(strict_types=1);

namespace Doctrine\Website\Git;

use Doctrine\Website\ProcessFactory;

use function array_map;
use function explode;
use function in_array;
use function ltrim;
use function preg_match_all;
use function sprintf;
use function str_starts_with;
use function strlen;
use function substr;

/** @final */
class TagBranchGuesser
{
    private const string COMMAND = 'cd %s && git branch -a';

    public function __construct(
        private ProcessFactory $processFactory,
    ) {
    }

    public function guessTagBranchName(string $repositoryPath, Tag $tag): string|null
    {
        $tagBranchName = $this->generateTagBranchSlug($tag);

        if ($tagBranchName === null) {
            return null;
        }

        $branchNames = $this->getBranchNames($repositoryPath);

        $guesses = [
            $tagBranchName,
            sprintf('%s.x', $tagBranchName),
        ];

        foreach ($guesses as $branchName) {
            if (in_array($branchName, $branchNames, true)) {
                return $branchName;
            }
        }

        return null;
    }

    /** @return list<string> */
    public function getBranchNames(string $repositoryPath): array
    {
        $command = sprintf(self::COMMAND, $repositoryPath);

        $process = $this->processFactory->run($command);

        $lines = array_map('trim', explode("\n", $process->getOutput()));

        $branchNames = [];

        foreach ($lines as $line) {
            if (! str_starts_with($line, 'remotes/origin/')) {
                continue;
            }

            $branchNames[] = substr($line, strlen('remotes/origin/'));
        }

        return $branchNames;
    }

    public function generateTagBranchSlug(Tag $tag): string|null
    {
        $versionSlug = ltrim($tag->getName(), 'v');

        preg_match_all('/([0-9]+).([0-9]+)(.*)/', $versionSlug, $matches);

        if (! isset($matches[1][0])) {
            return null;
        }

        return $matches[1][0] . '.' . $matches[2][0];
    }
}
