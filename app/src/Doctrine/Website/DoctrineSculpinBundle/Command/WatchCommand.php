<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('doctrine:watch')
            ->setDescription('Watch for changes to the website source code and build.')
            ->addArgument('build-dir', InputArgument::REQUIRED, 'The directory where the website is built')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernelRootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $rootDir = realpath($kernelRootDir.'/..');
        $buildDir = $input->getArgument('build-dir');

        if (!$buildDir) {
            throw new InvalidArgumentException('You must pass the build-dir argument.');
        }

        if (!is_dir($buildDir)) {
            throw new InvalidArgumentException(sprintf('The build directory %s does not exist.', $buildDir));
        }

        $buildScriptPath = $rootDir.'/build dev';

        $startPaths = [
            $rootDir.'/app/*',
            $rootDir.'/source/*',
        ];

        $lastTime = time();

        while (true) {
            $files = $this->recursiveGlob($startPaths);

            foreach ($files as $file) {
                $time = filemtime($file);

                if ($time > $lastTime) {
                    $lastTime = time();

                    echo sprintf("%s was changed. Building...\n", $file);

                    echo shell_exec($buildScriptPath)."\n";

                    file_put_contents($buildDir.'/changed', time());
                }
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
                if (is_dir($file)) {
                    $dirPath = $file.'/*';

                    $dirFiles = $this->recursiveGlob([$dirPath]);

                    $allFiles = array_merge($allFiles, $dirFiles);
                }
            }
        }

        return $allFiles;
    }
}
