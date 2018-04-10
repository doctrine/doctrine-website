<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Directive;
use Gregwar\RST\Environment;
use Gregwar\RST\Parser;

class ToctreeDirective extends Directive
{
    public function getName()
    {
        return 'toctree';
    }

    public function process(Parser $parser, $node, $variable, $data, array $options)
    {
        $environment = $parser->getEnvironment();
        $kernel = $parser->getKernel();
        $files = array();

        foreach (explode("\n", $node->getValue()) as $file) {
            $file = trim($file);

            if (isset($options['glob']) && strpos($file, '*') !== false) {
                $globPattern = $file;

                $globFiles = $this->globSearch($environment, $globPattern);

                foreach ($globFiles as $globFile) {
                    $dependency = $this->getDependencyFromFile($environment, $globFile);

                    $environment->addDependency($dependency);
                    $files[] = $dependency;
                }

            } elseif ($file) {
                $dependency = $this->getDependencyFromFile($environment, $file);

                $environment->addDependency($dependency);
                $files[] = $dependency;
            }
        }

        $document = $parser->getDocument();
        $document->addNode($kernel->build('Nodes\TocNode', $files, $environment, $options));
    }

    public function wantCode()
    {
        return true;
    }

    private function globSearch(Environment $environment, string $globPattern)
    {
        $currentDirPath = realpath(rtrim($environment->absoluteRelativePath(''), '/'));
        $rootPath = rtrim(str_replace($environment->getDirName(), '', $currentDirPath), '/');
        $globPattern = str_replace($rootPath, '', $globPattern);

        $allFiles = [];

        $files =  glob($rootPath.$globPattern);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $dirPath = $file.'/*';

                $dirFiles = $this->globSearch($environment, $dirPath);

                $allFiles = array_merge($allFiles, $dirFiles);
            } else {
                // Trim the root path and the .rst extension. This is what the
                // RST parser requires to add a dependency.
                $file = str_replace([$rootPath.'/', '.rst'], '', $file);

                $allFiles[] = $file;
            }
        }

        return $allFiles;
    }

    private function getDependencyFromFile(Environment $environment, string $file)
    {
        $url = $environment->getUrl();

        $e = explode('/', $url);

        if (count($e) > 1) {
            unset($e[count($e) - 1]);
            $folderPath = implode('/', $e).'/';

            if (strpos($file, $folderPath) !== false) {
                $file = str_replace($folderPath, '', $file);
            } else {
                $file = str_repeat('../', count($e)).$file;
            }
        }

        return $file;
    }
}
