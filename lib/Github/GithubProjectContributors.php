<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Website\Model\Project;
use Github\Api\Repo;
use Github\Client;
use function sprintf;

class GithubProjectContributors
{
    /** @var FilesystemCache */
    private $filesystemCache;

    /** @var Client */
    private $githubClient;

    public function __construct(
        FilesystemCache $filesystemCache,
        Client $githubClient
    ) {
        $this->filesystemCache = $filesystemCache;
        $this->githubClient    = $githubClient;
    }

    /**
     * @return mixed[]
     */
    public function getProjectContributors(Project $project) : array
    {
        $id = sprintf('doctrine-%s-contributors-data', $project->getSlug());

        if ($this->filesystemCache->contains($id)) {
            return $this->filesystemCache->fetch($id);
        }

        /** @var Repo $repo */
        $repo = $this->githubClient->api('repo');

        $contributors = $repo->statistics('doctrine', $project->getRepositoryName());

        $this->filesystemCache->save($id, $contributors, 86400);

        return $contributors;
    }
}
