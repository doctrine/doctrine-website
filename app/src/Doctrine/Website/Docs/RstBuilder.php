<?php

namespace Doctrine\Website\Docs;

use Gregwar\RST\Builder;
use Gregwar\RST\Parser;

/**
 * Override parseAll method and remove file exists check because we have references
 * to files in the rst that don't exist. Remove this after docs get fixed after the switch
 * to the new site.
 */
class RstBuilder extends Builder
{
    protected function parseAll()
    {
        $this->display('* Parsing files');
        while ($file = $this->getFileToParse()) {
            $this->display(' -> Parsing '.$file.'...');
            // Process the file
            $rst = $this->getRST($file);
            $parser = new Parser(null, $this->kernel);

            $environment = $parser->getEnvironment();
            $environment->setMetas($this->metas);
            $environment->setCurrentFilename($file);
            $environment->setCurrentDirectory($this->directory);
            $environment->setTargetDirectory($this->targetDirectory);
            $environment->setErrorManager($this->errorManager);

            foreach ($this->beforeHooks as $hook) {
                $hook($parser);
            }

            // Add this back/remove this class once docs are fixed
            // if (!file_exists($rst)) {
            //     $this->errorManager->error('Can\'t parse the file '.$rst);
            //     continue;
            // }

            $document = $this->documents[$file] = $parser->parseFile($rst);

            // Calling all the post-process hooks
            foreach ($this->hooks as $hook) {
                $hook($document);
            }

            // Calling the kernel document tweaking
            $this->kernel->postParse($document);

            $dependencies = $document->getEnvironment()->getDependencies();

            if ($dependencies) {
                $this->display(' -> Scanning dependencies of '.$file.'...');
                // Scan the dependencies for this document
                foreach ($dependencies as $dependency) {
                    $this->scan($dependency);
                }
            }

            // Append the meta for this document
            $this->metas->set(
                $file,
                $this->getUrl($document),
                $document->getTitle(),
                $document->getTitles(),
                $document->getTocs(),
                filectime($rst),
                $dependencies
            );
        }
    }
}
