<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Git\Tag;
use Doctrine\Website\Git\TagBranchGuesser;
use Doctrine\Website\Git\TagReader;

use function array_filter;
use function array_values;

/** @final */
class ProjectVersionsReader
{
    public function __construct(
        private readonly TagReader $tagReader,
        private readonly TagBranchGuesser $tagBranchGuesser,
    ) {
    }

    /** @return mixed[] */
    public function readProjectVersions(string $repositoryPath): array
    {
        $tags = $this->getProjectTags($repositoryPath);

        $versions = [];

        foreach ($tags as $tag) {
            $branchSlug = $this->tagBranchGuesser
                ->generateTagBranchSlug($tag);

            if ($branchSlug === null) {
                continue;
            }

            $branchName = $this->tagBranchGuesser
                ->guessTagBranchName($repositoryPath, $tag);

            if (isset($versions[$branchSlug])) {
                $versions[$branchSlug]['tags'][] = $tag;

                continue;
            }

            // if 0.x release doesn't have an associated branch, skip entry
            if ($tag->isMajorReleaseZero() && $branchName === null) {
                continue;
            }

            $versions[$branchSlug] = [
                'name' => $branchSlug,
                'slug' => $branchSlug,
                'branchName' => $branchName,
                'tags' => [$tag],
            ];
        }

        return array_values($versions);
    }

    /** @return mixed[] */
    private function getProjectTags(string $repositoryPath): array
    {
        $tags = $this->tagReader->getRepositoryTags($repositoryPath);

        return array_filter($tags, static function (Tag $tag): bool {
            return ! $tag->isDev();
        });
    }
}
