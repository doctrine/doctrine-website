<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Tests\TestCase;
use Github\Api\Repo;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\MockObject\MockObject;

use function sprintf;

class ProjectGitSyncerTest extends TestCase
{
    private ProcessFactory&MockObject $processFactory;

    private string $projectsDir;

    private ProjectGitSyncer $projectGitSyncer;

    private Repo&MockObject $githubRepo;

    protected function setUp(): void
    {
        vfsStream::setup('projects', null, [
            'orm' => ['.git' => []],
        ]);

        $this->processFactory = $this->createMock(ProcessFactory::class);
        $this->projectsDir    = vfsStream::url('projects');
        $this->githubRepo     = $this->createMock(Repo::class);

        $this->projectGitSyncer = new ProjectGitSyncer(
            $this->processFactory,
            $this->githubRepo,
            $this->projectsDir,
        );
    }

    public function testInitRepository(): void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with(sprintf(
                'git clone https://github.com/doctrine/\'%s\'.git \'%s/%s\'',
                $repositoryName,
                $this->projectsDir,
                $repositoryName,
            ));

        $this->projectGitSyncer->initRepository($repositoryName);
    }

    public function testInitRepositoryAlreadyInitialized(): void
    {
        $repositoryName = 'orm';

        $this->processFactory->expects(self::never())
            ->method('run');

        $this->projectGitSyncer->initRepository($repositoryName);
    }

    public function testSyncRepository(): void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::once())
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

        $this->processFactory->expects(self::once())
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

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with(sprintf(
                'cd \'%s/example-project\' && git clean -xdf && git checkout origin/\'1.0\'',
                $this->projectsDir,
            ));

        $this->projectGitSyncer->checkoutBranch($repositoryName, $branchName);
    }

    public function testIsRepositoryInitialized(): void
    {
        self::assertTrue($this->projectGitSyncer->isRepositoryInitialized('orm'));
        self::assertFalse($this->projectGitSyncer->isRepositoryInitialized('foo'));
    }

    public function testCheckoutTag(): void
    {
        $this->processFactory->expects(self::once())
            ->method('run')
            ->with("cd 'vfs://projects/example-project' && git clean -xdf && git checkout tags/'1.0.0'");

        $this->projectGitSyncer->checkoutTag('example-project', '1.0.0');
    }
}
