<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileRepository;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFilesBuilder;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\WebsiteBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class WebsiteBuilderTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var Filesystem|MockObject */
    private $filesystem;

    /** @var SourceFileRepository|MockObject */
    private $sourceFileRepository;

    /** @var SourceFilesBuilder|MockObject */
    private $sourceFilesBuilder;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $cacheDir;

    /** @var string */
    private $webpackBuildDir;

    /** @var WebsiteBuilder|MockObject */
    private $websiteBuilder;

    protected function setUp(): void
    {
        $this->processFactory       = $this->createMock(ProcessFactory::class);
        $this->projectRepository    = $this->createMock(ProjectRepository::class);
        $this->filesystem           = $this->createMock(Filesystem::class);
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);
        $this->sourceFilesBuilder   = $this->createMock(SourceFilesBuilder::class);
        $this->rootDir              = '/data/doctrine-website-build-staging';
        $this->cacheDir             = '/data/doctrine-website-build-staging/cache';
        $this->webpackBuildDir      = '/data/doctrine-website-build-staging/.webpack-build';

        $this->websiteBuilder = $this->getMockBuilder(WebsiteBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectRepository,
                $this->filesystem,
                $this->sourceFileRepository,
                $this->sourceFilesBuilder,
                $this->rootDir,
                $this->cacheDir,
                $this->webpackBuildDir,
            ])
            ->setMethods(['filePutContents'])
            ->getMock();
    }

    public function testBuild(): void
    {
        $output   = $this->createMock(OutputInterface::class);
        $buildDir = '/data/doctrine-website-build-staging';
        $env      = 'staging';

        $this->filesystem->expects(self::at(0))
            ->method('remove')
            ->with([]);

        $this->filesystem->expects(self::at(1))
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

        $this->filesystem->expects(self::at(2))
            ->method('mirror')
            ->with($this->webpackBuildDir, $buildDir . '/frontend');

        $this->filesystem->expects(self::at(3))
            ->method('mirror')
            ->with($this->cacheDir . '/data', $buildDir . '/website-data');

        $this->websiteBuilder->build($output, $buildDir, $env);
    }
}
