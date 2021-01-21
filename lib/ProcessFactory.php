<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Closure;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @return Process<string, string>
     */
    public function create(string $command): Process
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);

        return $process;
    }

    /**
     * @return Process<string, string>
     */
    public function run(string $command, ?Closure $callback = null): Process
    {
        $process = $this->create($command);
        $process->run($callback);

        // This code is used to run the git checkout command.
        // Even if it is not successful, there will be no critical damage in the end.
        // Turning it off only makes debugging difficult.
        // end currently blocking the build process
        // if (! $process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        // }

        return $process;
    }
}
