<?php

namespace Doctrine\Website\Tests;

use Doctrine\Website\ProcessFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends TestCase
{
    /** @var ProcessFactory */
    private $processFactory;

    protected function setUp()
    {
        $this->processFactory = new ProcessFactory();
    }

    public function testCreate()
    {
        $command = 'ls -la';

        $process = $this->processFactory->create($command);

        $this->assertEquals($command, $process->getCommandLine());
        $this->assertInstanceOf(Process::class, $process);
    }

    public function testRun()
    {
        $process = $this->processFactory->run('echo "test"');

        $this->assertEquals("test\n", $process->getOutput());
    }
}
