<?php

declare(strict_types=1);

namespace Doctrine\Website\Twitter;

use Doctrine\StaticWebsiteGenerator\Routing\Router;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function sprintf;

class TweetRelease
{
    /** @var CreateTweet */
    private $createTweet;

    /** @var Router */
    private $router;

    public function __construct(CreateTweet $createTweet, Router $router)
    {
        $this->createTweet = $createTweet;
        $this->router      = $router;
    }

    public function __invoke(
        Project $project,
        ProjectVersion $projectVersion,
        Tag $tag
    ): bool {
        $message = sprintf(
            'Released Doctrine %s %s %s',
            $project->getShortName(),
            $tag->getDisplayName(),
            $this->router->generate('project_version', [
                'slug' => $project->getSlug(),
                'versionSlug' => $projectVersion->getSlug(),
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        return $this->createTweet->__invoke($message);
    }
}
