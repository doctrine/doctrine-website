<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Github\GithubClientProvider;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Tests\TestCase;
use Github\Api\Repo;
use Github\Client;
use PHPUnit\Framework\MockObject\MockObject;

use function sprintf;

class ProjectGitSyncerTest extends TestCase
{
    /** @var ProcessFactory&MockObject */
    private $processFactory;

    /** @var string */
    private $projectsDir;

    /** @var ProjectGitSyncer */
    private $projectGitSyncer;

    /** @var Repo&MockObject */
    private $githubRepo;

    protected function setUp(): void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
        $this->projectsDir    = __DIR__;
        $this->githubRepo     = $this->createMock(Repo::class);
        $githubClientProvider = $this->createMock(GithubClientProvider::class);
        $githubClient         = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['repo'])
            ->getMock();

        $githubClient->method('repo')
            ->willReturn($this->githubRepo);

        $githubClientProvider->method('getGithubClient')
            ->willReturn($githubClient);

        $this->projectGitSyncer = new ProjectGitSyncer(
            $this->processFactory,
            $githubClientProvider,
            $this->projectsDir,
        );
    }

    public function testInitRepository(): void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'git clone https://github.com/doctrine/\'%s\'.git \'%s/%s\'',
                $repositoryName,
                $this->projectsDir,
                $repositoryName,
            ));

        $this->projectGitSyncer->initRepository($repositoryName);
    }

    public function testSyncRepository(): void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'cd \'%s/example-project\' && git clean -xdf && git fetch origin',
                $this->projectsDir,
            ));

        $this->projectGitSyncer->syncRepository($repositoryName);
    }

    public function testCheckoutDefaultBranch(): void
    {
        $repositoryName = 'example-project';

        $this->githubRepo->expects(self::once())
            ->method('show')
            ->with('doctrine', $repositoryName)
            ->willReturn(['default_branch' => '1.0']);

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'cd \'%s/example-project\' && git clean -xdf && git checkout origin/\'1.0\'',
                $this->projectsDir,
            ));

        $this->projectGitSyncer->checkoutDefaultBranch($repositoryName);
    }

    public function testCheckoutBranch(): void
    {
        $repositoryName = 'example-project';
        $branchName     = '1.0';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'cd \'%s/example-project\' && git clean -xdf && git checkout origin/\'1.0\'',
                $this->projectsDir,
            ));

        $this->projectGitSyncer->checkoutBranch($repositoryName, $branchName);
    }
}
