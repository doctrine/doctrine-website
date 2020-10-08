<?php

declare(strict_types=1);

namespace Doctrine\Website\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use RuntimeException;

use function json_encode;
use function sprintf;

class TwitterOAuthCreateTweet implements CreateTweet
{
    /** @var TwitterOAuth */
    private $twitter;

    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    public function __invoke(string $message): bool
    {
        $result = (array) $this->twitter->post('statuses/update', ['status' => $message]);

        if (isset($result['id'])) {
            return true;
        }

        throw new RuntimeException(
            sprintf('Failed to create tweet: %s', json_encode($result))
        );
    }
}
