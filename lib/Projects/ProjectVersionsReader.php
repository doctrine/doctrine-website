<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Git\Tag;
use Doctrine\Website\Git\TagBranchGuesser;
use Doctrine\Website\Git\TagReader;
use function array_filter;
use function array_values;

class ProjectVersionsReader
{
    /** @var TagReader */
    private $tagReader;

    /** @var TagBranchGuesser */
    private $tagBranchGuesser;

    public function __construct(TagReader $tagReader, TagBranchGuesser $tagBranchGuesser)
    {
        $this->tagReader        = $tagReader;
        $this->tagBranchGuesser = $tagBranchGuesser;
    }

    /**
     * @return mixed[]
     */
    public function readProjectVersions(string $repositoryPath) : array
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

            $versions[$branchSlug] = [
                'name' => $branchSlug,
                'slug' => $branchSlug,
                'branchName' => $branchName,
                'tags' => [$tag],
            ];
        }

        return array_values($versions);
    }

    /**
     * @return mixed[]
     */
    private function getProjectTags(string $repositoryPath) : array
    {
        $tags = $this->tagReader->getRepositoryTags($repositoryPath);

        return array_filter($tags, static function (Tag $tag) {
            if ($tag->isDev()) {
                return false;
            }

            return true;
        });
    }
}
