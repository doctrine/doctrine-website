<?php

namespace Doctrine\Website\Tests\DoctrineSculpinBundle\Command;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Sculpin\Bundle\SculpinBundle\HttpKernel\KernelFactory;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Process\Process;

/** @group functional */
class BuildWebsiteCommandFunctionalTest extends TestCase
{
    public function testCommand()
    {
        $process = new Process(__DIR__.'/../../../../../../doctrine build-website');
        $process->setTimeout(null);
        $process->run();

        $success = $process->isSuccessful();

        if (!$success) {
            throw new RuntimeException($process->getOutput());
        }

        $this->assertTrue($success);
    }
}
