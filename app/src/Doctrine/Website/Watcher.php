<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Symfony\Component\Console\Output\OutputInterface;
use function array_merge;
use function file_put_contents;
use function filemtime;
use function glob;
use function is_dir;
use function realpath;
use function sprintf;
use function time;

class Watcher
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $kernelRootDir;

    public function __construct(ProcessFactory $processFactory, string $kernelRootDir)
    {
        $this->processFactory = $processFactory;
        $this->kernelRootDir  = $kernelRootDir;
    }

    public function watch(string $buildDir, OutputInterface $output) : void
    {
        $rootDir = realpath($this->kernelRootDir . '/..');

        $buildScriptPath = sprintf(
            '%s/doctrine build-website %s --env=dev',
            $rootDir,
            $buildDir
        );

        $startPaths = [
            $rootDir . '/app/*',
            $rootDir . '/source/*',
        ];

        $lastTime = time();

        while (true) {
            $files = $this->recursiveGlob($startPaths);

            foreach ($files as $file) {
                $time = filemtime($file);

                if ($time <= $lastTime) {
                    continue;
                }

                $lastTime = time();

                $output->writeln(sprintf('%s was changed. Building...', $file));

                $this->processFactory->run($buildScriptPath, function ($type, $buffer) use ($output) : void {
                    $output->write($buffer);
                });

                file_put_contents($buildDir . '/changed', time());
            }
        }
    }

    private function recursiveGlob(array $paths)
    {
        $allFiles = [];

        foreach ($paths as $path) {
            $files =  glob($path);

            $allFiles = array_merge($allFiles, $files);

            foreach ($files as $file) {
                if (! is_dir($file)) {
                    continue;
                }

                $dirPath = $file . '/*';

                $dirFiles = $this->recursiveGlob([$dirPath]);

                $allFiles = array_merge($allFiles, $dirFiles);
            }
        }

        return $allFiles;
    }
}
