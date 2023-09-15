<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Github;

use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\Tests\TestCase;
use Github\AuthMethod;
use Github\Client;
use InvalidArgumentException;

class GithubClientProviderTest extends TestCase
{
    public function testGetGithubClient(): void
    {
        $githubClient    = $this->createMock(Client::class);
        $githubHttpToken = '1234';

        $githubClientProvider = new GithubClientProvider($githubClient, $githubHttpToken);

        $githubClient->expects(self::once())
            ->method('authenticate')
            ->with($githubHttpToken, '', AuthMethod::ACCESS_TOKEN);

        $githubClientResult = $githubClientProvider->getGithubClient();

        self::assertSame($githubClient, $githubClientResult);

        $githubClientResult = $githubClientProvider->getGithubClient();

        self::assertSame($githubClient, $githubClientResult);
    }

    public function testGetGithubClientWithMissingToken(): void
    {
        $githubClient    = $this->createMock(Client::class);
        $githubHttpToken = '';

        $this->expectException(InvalidArgumentException::class);

        new GithubClientProvider($githubClient, $githubHttpToken);
    }
}
