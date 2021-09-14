<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\Deployer;
use Doctrine\Website\ProcessFactory;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DeployerTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    protected function setUp(): void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
    }

    public function testDeployDev(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $output = $this->createMock(OutputInterface::class);

        $deployer = $this->getDeployer('dev', '1234', '1234');

        $deployer->deploy($output);
    }

    public function testDeployStagingNothingChanged(): void
    {
        $output = $this->createMock(OutputInterface::class);
        $output->expects(self::once())
            ->method('writeln')
            ->with('Nothing has changed. No need to deploy!');

        $deployer = $this->getDeployer('staging', '1234', '1234');

        $deployer->deploy($output);
    }

    public function testDeployStaging(): void
    {
        $output = $this->createMock(OutputInterface::class);
        $output->expects(self::once())
            ->method('writeln')
            ->with('Deploying website for <info>staging</info> environment.');

        $deployer = $this->getDeployer('staging', '1235', '1234');

        $process = $this->createMock(Process::class);

        $this->processFactory->expects(self::at(0))
            ->method('run')
            ->with('cp vfs://data/deploy-staging vfs://data/last-deploy-staging')
            ->willReturn($process);

        $this->processFactory->expects(self::at(1))
            ->method('run')
            ->with('cd vfs://data && git fetch && git checkout 1234 && git pull origin 1234 && php composer.phar install --no-dev && yarn install')
            ->willReturn($process);

        $this->processFactory->expects(self::at(2))
            ->method('run')
            ->with('cd vfs://data && ./bin/console migrations:migrate --no-interaction --env=staging && ./bin/console build-all vfs://data --env=staging --publish')
            ->willReturn($process);

        $deployer->deploy($output);
    }

    private function getDeployer(string $env, string $lastDeployContent, string $deployContent): Deployer
    {
        $baseDir   = vfsStream::setup('data');
        $structure = [
            'last-deploy-staging' => $lastDeployContent,
            'deploy-staging' => $deployContent,
        ];
        vfsStream::create($structure, $baseDir);
        $path = vfsStream::url('data');

        return new Deployer($this->processFactory, $env, $path);
    }
}
