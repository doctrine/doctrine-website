<?php

namespace Doctrine\Website\Tests;

use Doctrine\Website\Deployer;
use Doctrine\Website\ProcessFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DeployerTest extends TestCase
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $projectsPath;

    /** @var Deployer */
    private $deployer;

    protected function setUp()
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
        $this->projectsPath = '/data/doctrine';
    }

    public function testDeployDev()
    {
        $this->expectException(InvalidArgumentException::class);

        $output = $this->createMock(OutputInterface::class);

        $deployer = $this->getMockDeployer('dev');

        $deployer->deploy($output);
    }

    public function testDeployStagingNothingChanged()
    {
        $output = $this->createMock(OutputInterface::class);

        $deployer = $this->getMockDeployer('staging');

        $deployer->expects($this->once())
            ->method('getDeploy')
            ->willReturn('1234');

        $deployer->expects($this->once())
            ->method('getLastDeploy')
            ->willReturn('1234');

        $output->expects($this->once())
            ->method('writeln')
            ->with('Nothing has changed. No need to deploy!');

        $deployer->deploy($output);
    }

    public function testDeployStaging()
    {
        $output = $this->createMock(OutputInterface::class);

        $deployer = $this->getMockDeployer('staging');

        $deployer->expects($this->once())
            ->method('getDeploy')
            ->willReturn('1234');

        $deployer->expects($this->once())
            ->method('getLastDeploy')
            ->willReturn('1235');

        $output->expects($this->once())
            ->method('writeln')
            ->with('Deploying website for <info>staging</info> environment.');

        $process = $this->createMock(Process::class);

        $this->processFactory->expects($this->at(0))
            ->method('run')
            ->with('cd /data/doctrine-website-sculpin-staging && git fetch && git checkout 1234 && git pull origin 1234 && ./doctrine build-docs --api && ./doctrine build-website /data/doctrine-website-sculpin-build-staging --env=staging --publish')
            ->willReturn($process);

        $this->processFactory->expects($this->at(1))
            ->method('run')
            ->with('cp /data/doctrine-website-sculpin-staging/deploy-staging /data/doctrine-website-sculpin-staging/last-deploy-staging')
            ->willReturn($process);

        $deployer->deploy($output);
    }

    private function getMockDeployer(string $env) : Deployer
    {
        return $this->getMockBuilder(Deployer::class)
            ->setConstructorArgs([
                $this->processFactory,
                $this->projectsPath,
                $env
            ])
            ->setMethods(['getDeploy', 'getLastDeploy'])
            ->getMock();
    }
}
