<?php

declare(strict_types=1);

namespace Doctrine\Website\Release;

use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Twitter\TweetRelease;
use InvalidArgumentException;

use function count;
use function explode;
use function ltrim;
use function sprintf;

class AnnounceRelease
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var TweetRelease */
    private $tweetRelease;

    public function __construct(
        ProjectRepository $projectRepository,
        TweetRelease $tweetRelease
    ) {
        $this->projectRepository = $projectRepository;
        $this->tweetRelease      = $tweetRelease;
    }

    public function __invoke(string $projectSlug, string $tag): bool
    {
        $project = $this->projectRepository->findOneBySlug($projectSlug);

        $tagSlug = ltrim($tag, 'v');

        $e = explode('.', $tagSlug);

        if (count($e) !== 3) {
            throw new InvalidArgumentException(
                sprintf('Tag "%s" improperly formatted. Expected format "1.0.0".', $tag)
            );
        }

        $versionSlug = sprintf('%d.%d', $e[0], $e[1]);

        $version = $project->getVersion($versionSlug);

        $tag = $version->getTag($tagSlug);

        return $this->tweetRelease->__invoke($project, $version, $tag);
    }
}
