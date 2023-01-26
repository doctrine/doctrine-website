<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Github;

use Doctrine\Website\Github\ProdGithubProjectContributors;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Tests\TestCase;
use Github\Api\Repo;
use Github\Client;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

use function sprintf;

class ProdGithubProjectContributorsTest extends TestCase
{
    private ArrayAdapter $cache;
    private Client&MockObject $githubClient;
    private ProdGithubProjectContributors $githubProjectContributors;

    protected function setUp(): void
    {
        $this->cache        = new ArrayAdapter();
        $this->githubClient = $this->createMock(Client::class);

        $this->githubProjectContributors = new ProdGithubProjectContributors(
            $this->cache,
            $this->githubClient,
        );
    }

    public function testGetProjectContributors(): void
    {
        $id = 'doctrine-orm-contributors-data';

        $expected = [['author' => ['login' => 'jwage']]];

        $project = $this->createMock(Project::class);

        $project->expects(self::once())
            ->method('getSlug')
            ->willReturn('orm');

        $project->expects(self::once())
            ->method('getRepositoryName')
            ->willReturn('doctrine2');

        $repo = $this->createMock(Repo::class);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($repo);

        $repo->expects(self::once())
            ->method('statistics')
            ->with('doctrine', 'doctrine2')
            ->willReturn($expected);

        $projectContributors = $this->githubProjectContributors->getProjectContributors($project);

        self::assertEquals($expected, $projectContributors);
        self::assertTrue($this->cache->hasItem($id));
    }

    public function testGetProjectContributorsCache(): void
    {
        $expected = [['author' => ['login' => 'jwage']]];

        $project = $this->createMock(Project::class);

        $project->expects(self::once())
            ->method('getSlug')
            ->willReturn('orm');

        $this->cache->get('doctrine-orm-contributors-data', static fn () => $expected);

        $projectContributors = $this->githubProjectContributors->getProjectContributors($project);

        self::assertEquals($expected, $projectContributors);
    }

    public function testGetProjectContributorsThrowsRuntimeExceptionWhenGitHubReturnsEmptyArray(): void
    {
        $expected = [];

        $project = $this->createMock(Project::class);

        $project->expects(self::once())
            ->method('getSlug')
            ->willReturn('orm');

        $project->expects(self::once())
            ->method('getRepositoryName')
            ->willReturn('doctrine2');

        $repo = $this->createMock(Repo::class);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($repo);

        $repo->expects(self::once())
            ->method('statistics')
            ->with('doctrine', 'doctrine2')
            ->willReturn($expected);

        try {
            $this->githubProjectContributors->getProjectContributors($project);

            self::fail(sprintf('An %s was expected to be raised.', RuntimeException::class));
        } catch (RuntimeException $e) {
            self::assertSame('The GitHub API should not return an empty array here for repository doctrine2.', $e->getMessage());
        }

        self::assertSame(['doctrine-orm-contributors-data' => null], $this->cache->getValues());
    }
}
