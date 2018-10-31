<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Github;

use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\Tests\TestCase;
use Github\Client;
use PHPUnit\Framework\MockObject\MockObject;

class GithubClientProviderTest extends TestCase
{
    /** @var Client|MockObject */
    private $githubClient;

    /** @var string */
    private $githubHttpToken;

    /** @var GithubClientProvider */
    private $githubClientProvider;

    public function testGetGithubClient() : void
    {
        $this->githubClient->expects(self::exactly(1))
            ->method('authenticate')
            ->with($this->githubHttpToken, '', 'http_token');

        $githubClient = $this->githubClientProvider->getGithubClient();

        self::assertSame($this->githubClient, $githubClient);

        $githubClient = $this->githubClientProvider->getGithubClient();

        self::assertSame($this->githubClient, $githubClient);
    }

    protected function setUp() : void
    {
        $this->githubClient    = $this->createMock(Client::class);
        $this->githubHttpToken = '1234';

        $this->githubClientProvider = new GithubClientProvider($this->githubClient, $this->githubHttpToken);
    }
}
