<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\Deployer;
use Doctrine\Website\ProcessFactory;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DeployerTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    protected function setUp() : void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
    }

    public function testDeployDev() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $output = $this->createMock(OutputInterface::class);

        $deployer = $this->getMockDeployer('dev');

        $deployer->deploy($output);
    }

    public function testDeployStagingNothingChanged() : void
    {
        $output = $this->createMock(OutputInterface::class);

        /** @var Deployer|MockObject $deployer */
        $deployer = $this->getMockDeployer('staging');

        $deployer->expects(self::once())
            ->method('getDeploy')
            ->willReturn('1234');

        $deployer->expects(self::once())
            ->method('getLastDeploy')
            ->willReturn('1234');

        $output->expects(self::once())
            ->method('writeln')
            ->with('Nothing has changed. No need to deploy!');

        $deployer->deploy($output);
    }

    public function testDeployStaging() : void
    {
        $output = $this->createMock(OutputInterface::class);

        /** @var Deployer|MockObject $deployer */
        $deployer = $this->getMockDeployer('staging');

        $deployer->expects(self::once())
            ->method('getDeploy')
            ->willReturn('1234');

        $deployer->expects(self::once())
            ->method('getLastDeploy')
            ->willReturn('1235');

        $output->expects(self::once())
            ->method('writeln')
            ->with('Deploying website for <info>staging</info> environment.');

        $process = $this->createMock(Process::class);

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with('cp /data/doctrine-website-staging/deploy-staging /data/doctrine-website-staging/last-deploy-staging')
            ->willReturn($process);

        $this->processFactory->expects(self::at(1))
            ->method('run')
            ->with('cd /data/doctrine-website-staging && git fetch && git checkout 1234 && git pull origin 1234 && php composer.phar install --no-dev')
            ->willReturn($process);

        $this->processFactory->expects(self::at(2))
            ->method('run')
            ->with('cd /data/doctrine-website-staging && ./bin/console build-docs --api --sync-git && ./bin/console build-website /data/doctrine-website-build-staging --env=staging --publish')
            ->willReturn($process);

        $deployer->deploy($output);
    }

    private function getMockDeployer(string $env) : Deployer
    {
        return $this->getMockBuilder(Deployer::class)
            ->setConstructorArgs([
                $this->processFactory,
                $env,
            ])
            ->setMethods(['getDeploy', 'getLastDeploy'])
            ->getMock();
    }
}
