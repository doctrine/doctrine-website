<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Github;

use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\Tests\TestCase;
use Github\AuthMethod;
use Github\Client;
use PHPUnit\Framework\MockObject\MockObject;

class GithubClientProviderTest extends TestCase
{
    private Client&MockObject $githubClient;

    private string $githubHttpToken;

    private GithubClientProvider $githubClientProvider;

    public function testGetGithubClient(): void
    {
        $this->githubClient->expects(self::exactly(1))
            ->method('authenticate')
            ->with($this->githubHttpToken, '', AuthMethod::ACCESS_TOKEN);

        $githubClient = $this->githubClientProvider->getGithubClient();

        self::assertSame($this->githubClient, $githubClient);

        $githubClient = $this->githubClientProvider->getGithubClient();

        self::assertSame($this->githubClient, $githubClient);
    }

    protected function setUp(): void
    {
        $this->githubClient    = $this->createMock(Client::class);
        $this->githubHttpToken = '1234';

        $this->githubClientProvider = new GithubClientProvider($this->githubClient, $this->githubHttpToken);
    }
}
