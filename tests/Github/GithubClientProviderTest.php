<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Github;

use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\Tests\TestCase;
use Github\Api\Repo;
use Github\AuthMethod;
use Github\Client;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;

class GithubClientProviderTest extends TestCase
{
    public function testRepositories(): void
    {
        $githubRepo      = $this->createMock(Repo::class);
        $githubClient    = $this->createMock(Client::class);
        $cache           = $this->createMock(CacheItemPoolInterface::class);
        $githubHttpToken = '1234';

        $githubClient->expects(self::once())
            ->method('authenticate')
            ->with($githubHttpToken, '', AuthMethod::ACCESS_TOKEN);
        $githubClient->expects(self::once())
            ->method('addCache')
            ->with($cache);
        $githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($githubRepo);

        $githubClientProvider = new GithubClientProvider($githubClient, $cache, $githubHttpToken);

        $githubClientResult = $githubClientProvider->repositories();

        self::assertSame($githubRepo, $githubClientResult);
    }

    public function testGetGithubClientWithMissingToken(): void
    {
        $githubClient    = $this->createMock(Client::class);
        $cache           = $this->createMock(CacheItemPoolInterface::class);
        $githubHttpToken = '';

        $this->expectException(InvalidArgumentException::class);

        new GithubClientProvider($githubClient, $cache, $githubHttpToken);
    }
}
