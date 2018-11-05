<?php

declare(strict_types=1);

namespace Doctrine\Website\Github;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Website\Model\Project;
use Github\Api\Repo;
use Github\Client;
use RuntimeException;
use function sleep;
use function sprintf;

class ProdGithubProjectContributors implements GithubProjectContributors
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
     * @param Project[] $projects
     */
    public function warmProjectsContributors(array $projects) : void
    {
        foreach ($projects as $project) {
            $this->warmProjectContributors($project);
        }

        foreach ($projects as $project) {
            $this->waitForProjectContributorsData($project);
        }
    }

    public function warmProjectContributors(Project $project) : void
    {
        // Trigger api call to github to build statistics. The GitHub API may return
        // results right away, in that case we will go ahead and store the results in the cache.
        $this->doGetProjectContributors($project, false);
    }

    public function waitForProjectContributorsData(Project $project) : void
    {
        $count     = 1;
        $maxChecks = 15;
        $sleep     = 1;
        $waited    = 0;

        while ($count <= $maxChecks) {
            $contributors = $this->doGetProjectContributors($project, false);

            if ($contributors !== []) {
                return;
            }

            $sleep *= $count;
            $count++;

            sleep($sleep);

            $waited += $sleep;
        }

        throw new RuntimeException(
            sprintf(
                'Waited for %d seconds with %d checks but could not get contributor data.',
                $waited,
                $count
            )
        );
    }

    /**
     * @return mixed[]
     */
    public function getProjectContributors(Project $project) : array
    {
        return $this->doGetProjectContributors($project, true);
    }

    /**
     * @return mixed[]
     */
    private function doGetProjectContributors(Project $project, bool $throwOnEmpty) : array
    {
        $id = sprintf('doctrine-%s-contributors-data', $project->getSlug());

        if ($this->filesystemCache->contains($id)) {
            return $this->filesystemCache->fetch($id);
        }

        /** @var Repo $repo */
        $repo = $this->githubClient->api('repo');

        $repositoryName = $project->getRepositoryName();

        $contributors = $repo->statistics('doctrine', $repositoryName);

        if ($contributors !== []) {
            $this->filesystemCache->save($id, $contributors, 86400);
        }

        if ($contributors === [] && $throwOnEmpty) {
            throw new RuntimeException(sprintf(
                'The GitHub API should not return an empty array here for repository %s.',
                $repositoryName
            ));
        }

        return $contributors;
    }
}
