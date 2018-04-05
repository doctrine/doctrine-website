<?php

namespace Doctrine\Website;

use Closure;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    public function create(string $command) : Process
    {
        $process = new Process($command);
        $process->setTimeout(null);

        return $process;
    }

    public function run(string $command, Closure $callback = null) : Process
    {
        $process = $this->create($command);
        $process->run($callback);

        return $process;
    }
}
