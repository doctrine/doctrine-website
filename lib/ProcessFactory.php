<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Closure;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    /** @return Process<string, string> */
    public function create(string $command): Process
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);

        return $process;
    }

    /** @return Process<string, string> */
    public function run(string $command, Closure|null $callback = null): Process
    {
        $process = $this->create($command);
        $process->run($callback);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }
}
