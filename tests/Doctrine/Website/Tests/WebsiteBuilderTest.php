<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Dflydev\DotAccessConfiguration\Configuration;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\WebsiteBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use function realpath;
use function sprintf;

class WebsiteBuilderTest extends TestCase
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var Configuration */
    private $sculpinConfig;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $kernelRootDir;

    /** @var string */
    private $rootDir;

    /** @var WebsiteBuilder */
    private $websiteBuilder;

    protected function setUp() : void
    {
        $this->processFactory    = $this->createMock(ProcessFactory::class);
        $this->sculpinConfig     = $this->createMock(Configuration::class);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->filesystem        = $this->createMock(Filesystem::class);
        $this->kernelRootDir     = realpath(__DIR__ . '/../../../../app');
        $this->rootDir           = realpath($this->kernelRootDir . '/..');

        $this->websiteBuilder = $this->getMockBuilder(WebsiteBuilder::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->sculpinConfig,
                $this->projectRepository,
                $this->filesystem,
                $this->kernelRootDir,
            ])
            ->setMethods(['filePutContents'])
            ->getMock()
        ;
    }

    public function testBuild() : void
    {
        $output   = $this->createMock(OutputInterface::class);
        $buildDir = '/data/doctrine-website-build-staging';
        $env      = 'staging';
        $publish  = true;

        $this->processFactory->expects($this->at(0))
            ->method('run')
            ->with('cd /data/doctrine-website-build-staging && git pull origin master');

        $this->processFactory->expects($this->at(1))
            ->method('run')
            ->with(sprintf('php -d memory_limit=1024M %s/vendor/bin/sculpin generate --env=staging', $this->rootDir));

        $this->filesystem->expects($this->once())
            ->method('remove');

        $this->filesystem->expects($this->once())
            ->method('mirror')
            ->with(sprintf('%s/output_staging', $this->rootDir), '/data/doctrine-website-build-staging');

        $this->sculpinConfig->expects($this->once())
            ->method('get')
            ->with('url')
            ->willReturn('lcl.doctrine-project.org');

        $this->websiteBuilder->expects($this->once())
            ->method('filePutContents')
            ->with(
                '/data/doctrine-website-build-staging/CNAME',
                'lcl.doctrine-project.org'
            );

        $this->processFactory->expects($this->at(2))
            ->method('run')
            ->with('cd /data/doctrine-website-build-staging && git pull origin master && git add . --all && git commit -m"New version of Doctrine website" && git push origin master');

        $this->websiteBuilder->build($output, $buildDir, $env, $publish);
    }
}
