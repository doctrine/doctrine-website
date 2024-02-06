<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Github\Api\Repo;
use Github\AuthMethod;
use Github\Client;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;

use function assert;

final class GithubClientProvider
{
    private bool $authenticated = false;

    public function __construct(
        private readonly Client $githubClient,
        CacheItemPoolInterface $cache,
        private readonly string $githubHttpToken,
    ) {
        if ($githubHttpToken === '') {
            throw new InvalidArgumentException('You must configure a Github http token.');
        }

        $this->githubClient->addCache($cache);
    }

    public function repositories(): Repo
    {
        $repositories = $this->getGithubClient()->api('repo');

        assert($repositories instanceof Repo);

        return $repositories;
    }

    private function getGithubClient(): Client
    {
        if ($this->authenticated === false) {
            $this->githubClient->authenticate($this->githubHttpToken, '', AuthMethod::ACCESS_TOKEN);

            $this->authenticated = true;
        }

        return $this->githubClient;
    }
}
