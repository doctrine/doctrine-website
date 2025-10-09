<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\Model\Project;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileRepository;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFilesBuilder;
use Doctrine\Website\WebsiteBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class WebsiteBuilderTest extends TestCase
{
    private ProcessFactory&MockObject $processFactory;

    /** @var ProjectRepository<Project>&MockObject  */
    private ProjectRepository&MockObject $projectRepository;

    private Filesystem&MockObject $filesystem;

    private SourceFileRepository&MockObject $sourceFileRepository;

    private SourceFilesBuilder&MockObject $sourceFilesBuilder;

    private string $rootDir;

    private string $webpackBuildDir;

    private WebsiteBuilder&MockObject $websiteBuilder;

    protected function setUp(): void
    {
        $this->processFactory       = $this->createMock(ProcessFactory::class);
        $this->projectRepository    = $this->createMock(ProjectRepository::class);
        $this->filesystem           = $this->createMock(Filesystem::class);
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);
        $this->sourceFilesBuilder   = $this->createMock(SourceFilesBuilder::class);
        $this->rootDir              = '/data/doctrine-website-build-staging';
        $this->webpackBuildDir      = '/data/doctrine-website-build-staging/.webpack-build';

        $this->websiteBuilder = $this->getMockBuilder(WebsiteBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectRepository,
                $this->filesystem,
                $this->sourceFileRepository,
                $this->sourceFilesBuilder,
                $this->rootDir,
                $this->webpackBuildDir,
            ])
            ->onlyMethods(['filePutContents'])
            ->getMock();
    }

    public function testBuild(): void
    {
        $output   = $this->createMock(OutputInterface::class);
        $buildDir = '/data/doctrine-website-build-staging';
        $env      = 'staging';

        $this->filesystem->expects(self::exactly(2))
            ->method('remove')
            ->with([]);

        $this->websiteBuilder->expects(self::once())
            ->method('filePutContents')
            ->with(
                '/data/doctrine-website-build-staging/CNAME',
                'staging.doctrine-project.org',
            );

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with('cd /data/doctrine-website-build-staging && npm run build');

        $mirrored = [];

        $this->filesystem->method('mirror')
            ->willReturnCallback(static function (string $originDir, string $targetDir) use (&$mirrored): void {
                $mirrored[$originDir] = $targetDir;
            });

        $this->websiteBuilder->build($output, $buildDir, $env);

        self::assertSame($buildDir . '/frontend', $mirrored[$this->webpackBuildDir]);
    }
}
