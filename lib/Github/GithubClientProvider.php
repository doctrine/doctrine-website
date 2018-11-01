<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Github\Client;
use InvalidArgumentException;

class GithubClientProvider
{
    /** @var Client */
    private $githubClient;

    /** @var string */
    private $githubHttpToken;

    /** @var bool */
    private $authenticated = false;

    public function __construct(Client $githubClient, string $githubHttpToken)
    {
        if ($githubHttpToken === '') {
            throw new InvalidArgumentException('You must configure a Github http token.');
        }

        $this->githubClient    = $githubClient;
        $this->githubHttpToken = $githubHttpToken;
    }

    public function getGithubClient() : Client
    {
        if ($this->authenticated === false) {
            $this->githubClient->authenticate($this->githubHttpToken, '', 'http_token');

            $this->authenticated = true;
        }

        return $this->githubClient;
    }
}
