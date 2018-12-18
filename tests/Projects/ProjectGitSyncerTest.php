<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use function sprintf;

class ProjectGitSyncerTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var string */
    private $projectsDir;

    /** @var ProjectGitSyncer */
    private $projectGitSyncer;

    protected function setUp() : void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
        $this->projectsDir    = __DIR__;

        $this->projectGitSyncer = new ProjectGitSyncer(
            $this->processFactory,
            $this->projectsDir
        );
    }

    public function testInitRepository() : void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'git clone https://github.com/doctrine/%s.git %s/%s',
                $repositoryName,
                $this->projectsDir,
                $repositoryName
            ));

        $this->projectGitSyncer->initRepository($repositoryName);
    }

    public function testSync() : void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'cd %s/example-project && git clean -xdf && git fetch origin',
                $this->projectsDir
            ));

        $this->projectGitSyncer->sync($repositoryName);
    }

    public function testCheckoutMaster() : void
    {
        $repositoryName = 'example-project';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'cd %s/example-project && git clean -xdf && git checkout origin/master',
                $this->projectsDir
            ));

        $this->projectGitSyncer->checkoutMaster($repositoryName);
    }

    public function testCheckoutBranch() : void
    {
        $repositoryName = 'example-project';
        $branchName     = '1.0';

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'cd %s/example-project && git clean -xdf && git checkout origin/1.0',
                $this->projectsDir
            ));

        $this->projectGitSyncer->checkoutBranch($repositoryName, $branchName);
    }
}
