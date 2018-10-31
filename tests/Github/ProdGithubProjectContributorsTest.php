<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Github;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Website\Github\ProdGithubProjectContributors;
use Doctrine\Website\Model\Project;
use Github\Api\Repo;
use Github\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProdGithubProjectContributorsTest extends TestCase
{
    /** @var FilesystemCache|MockObject */
    private $filesystemCache;

    /** @var Client|MockObject */
    private $githubClient;

    /** @var ProdGithubProjectContributors */
    private $githubProjectContributors;

    protected function setUp() : void
    {
        $this->filesystemCache = $this->createMock(FilesystemCache::class);
        $this->githubClient    = $this->createMock(Client::class);

        $this->githubProjectContributors = new ProdGithubProjectContributors(
            $this->filesystemCache,
            $this->githubClient
        );
    }

    public function testGetProjectContributors() : void
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

        $this->filesystemCache->expects(self::once())
            ->method('contains')
            ->with($id)
            ->willReturn(false);

        $repo = $this->createMock(Repo::class);

        $this->githubClient->expects(self::once())
            ->method('api')
            ->with('repo')
            ->willReturn($repo);

        $repo->expects(self::once())
            ->method('statistics')
            ->with('doctrine', 'doctrine2')
            ->willReturn($expected);

        $this->filesystemCache->expects(self::once())
            ->method('save')
            ->with($id, $expected, 86400);

        $projectContributors = $this->githubProjectContributors->getProjectContributors($project);

        self::assertEquals($expected, $projectContributors);
    }

    public function testGetProjectContributorsCache() : void
    {
        $id = 'doctrine-orm-contributors-data';

        $expected = [['author' => ['login' => 'jwage']]];

        $project = $this->createMock(Project::class);

        $project->expects(self::once())
            ->method('getSlug')
            ->willReturn('orm');

        $this->filesystemCache->expects(self::once())
            ->method('contains')
            ->with($id)
            ->willReturn(true);

        $this->filesystemCache->expects(self::once())
            ->method('fetch')
            ->with($id)
            ->willReturn($expected);

        $projectContributors = $this->githubProjectContributors->getProjectContributors($project);

        self::assertEquals($expected, $projectContributors);
    }
}
