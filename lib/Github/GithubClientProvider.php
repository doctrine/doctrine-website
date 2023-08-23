<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Github\AuthMethod;
use Github\Client;
use InvalidArgumentException;

class GithubClientProvider
{
    private bool $authenticated = false;

    public function __construct(private Client $githubClient, private string $githubHttpToken)
    {
        if ($githubHttpToken === '') {
            throw new InvalidArgumentException('You must configure a Github http token.');
        }
    }

    public function getGithubClient(): Client
    {
        if ($this->authenticated === false) {
            $this->githubClient->authenticate($this->githubHttpToken, '', AuthMethod::ACCESS_TOKEN);

            $this->authenticated = true;
        }

        return $this->githubClient;
    }
}
