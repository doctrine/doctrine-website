<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Closure;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    public function create(string $command) : Process
    {
        $process = new Process($command);
        $process->setTimeout(null);

        return $process;
    }

    public function run(string $command, ?Closure $callback = null) : Process
    {
        $process = $this->create($command);
        $process->run($callback);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }
}
