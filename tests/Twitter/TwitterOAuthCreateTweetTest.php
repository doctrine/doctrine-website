<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twitter\TwitterOAuthCreateTweet;
use RuntimeException;

final class TwitterOAuthCreateTweetTest extends TestCase
{
    public function testInvokeSuccess(): void
    {
        $message = 'Tweet this!';

        $twitterOAuth = $this->createMock(TwitterOAuth::class);

        $twitterOAuth->expects(self::once())
            ->method('post')
            ->with('statuses/update', ['status' => $message])
            ->willReturn((object) ['id' => 1]);

        self::assertTrue((new TwitterOAuthCreateTweet($twitterOAuth))->__invoke($message));
    }

    public function testInvokeFailure(): void
    {
        $message = 'Tweet this!';

        $twitterOAuth = $this->createMock(TwitterOAuth::class);

        $twitterOAuth->expects(self::once())
            ->method('post')
            ->with('statuses/update', ['status' => $message])
            ->willReturn((object) ['message' => 'Failed']);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Failed to create tweet: {"message":"Failed"}');

        (new TwitterOAuthCreateTweet($twitterOAuth))->__invoke($message);
    }
}
