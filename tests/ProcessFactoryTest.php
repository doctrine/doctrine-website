<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\ProcessFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends TestCase
{
    /** @var ProcessFactory */
    private $processFactory;

    protected function setUp() : void
    {
        $this->processFactory = new ProcessFactory();
    }

    public function testCreate() : void
    {
        $command = 'ls -la';

        $process = $this->processFactory->create($command);

        self::assertSame($command, $process->getCommandLine());
        self::assertInstanceOf(Process::class, $process);
    }

    public function testRun() : void
    {
        $process = $this->processFactory->run('echo "test"');

        self::assertSame("test\n", $process->getOutput());
    }
}
