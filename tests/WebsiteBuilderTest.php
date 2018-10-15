<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\StaticWebsiteGenerator\Routing\Router;
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

    /** @var WebsiteBuilder|MockObject */
    private $websiteBuilder;

    /** @var Router|MockObject */
    private $router;

    protected function setUp() : void
    {
        $this->processFactory       = $this->createMock(ProcessFactory::class);
        $this->projectRepository    = $this->createMock(ProjectRepository::class);
        $this->filesystem           = $this->createMock(Filesystem::class);
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);
        $this->sourceFilesBuilder   = $this->createMock(SourceFilesBuilder::class);
        $this->router               = $this->createMock(Router::class);

        $this->websiteBuilder = $this->getMockBuilder(WebsiteBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectRepository,
                $this->filesystem,
                $this->sourceFileRepository,
                $this->sourceFilesBuilder,
                $this->router,
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
