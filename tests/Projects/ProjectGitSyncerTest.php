<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectVersion;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function sprintf;

class ProjectGitSyncerTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var string */
    private $projectsPath;

    /** @var ProjectGitSyncer */
    private $projectGitSyncer;

    protected function setUp() : void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
        $this->projectsPath   = __DIR__;

        $this->projectGitSyncer = new ProjectGitSyncer(
            $this->processFactory,
            $this->projectsPath
        );
    }

    public function testSync() : void
    {
        $project        = new Project([
            'repositoryName' => 'example-project-docs',
            'docsRepositoryName' => 'example-project',
        ]);
        $projectVersion = new ProjectVersion(['branchName' => '1.0']);

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with(sprintf(
                'git clone https://github.com/doctrine/example-project-docs.git %s/example-project-docs',
                $this->projectsPath
            ));

        $this->processFactory->expects(self::at(1))
            ->method('run')
            ->with(sprintf(
                'cd %s/example-project-docs && git clean -xdf && git fetch origin && git checkout origin/1.0',
                $this->projectsPath
            ));

        $this->processFactory->expects(self::at(2))
            ->method('run')
            ->with(sprintf(
                'git clone https://github.com/doctrine/example-project.git %s/example-project',
                $this->projectsPath
            ));

        $this->processFactory->expects(self::at(3))
            ->method('run')
            ->with(sprintf(
                'cd %s/example-project && git clean -xdf && git fetch origin && git checkout origin/1.0',
                $this->projectsPath
            ));

        $this->projectGitSyncer->sync($project, $projectVersion);
    }
}
