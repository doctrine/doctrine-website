<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\Builder\SourceFileBuilder;
use Doctrine\Website\Builder\SourceFileRepository;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\ProjectRepository;
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

    /** @var SourceFileBuilder|MockObject */
    private $sourceFileBuilder;

    /** @var WebsiteBuilder|MockObject */
    private $websiteBuilder;

    protected function setUp() : void
    {
        $this->processFactory       = $this->createMock(ProcessFactory::class);
        $this->projectRepository    = $this->createMock(ProjectRepository::class);
        $this->filesystem           = $this->createMock(Filesystem::class);
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);
        $this->sourceFileBuilder    = $this->createMock(SourceFileBuilder::class);

        $this->websiteBuilder = $this->getMockBuilder(WebsiteBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectRepository,
                $this->filesystem,
                $this->sourceFileRepository,
                $this->sourceFileBuilder,
            ])
            ->setMethods(['filePutContents'])
            ->getMock();
    }

    public function testBuild() : void
    {
        $output   = $this->createMock(OutputInterface::class);
        $buildDir = '/data/doctrine-website-build-staging';
        $env      = 'staging';
        $publish  = true;

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with('cd /data/doctrine-website-build-staging && git pull origin master');

        $this->filesystem->expects(self::once())
            ->method('remove');

        $this->websiteBuilder->expects(self::once())
            ->method('filePutContents')
            ->with(
                '/data/doctrine-website-build-staging/CNAME',
                'staging.doctrine-project.org'
            );

        $this->processFactory->expects(self::at(1))
            ->method('run')
            ->with('cd /data/doctrine-website-build-staging && git pull origin master && git add . --all && git commit -m"New version of Doctrine website" && git push origin master');

        $this->websiteBuilder->build($output, $buildDir, $env, $publish);
    }
}
