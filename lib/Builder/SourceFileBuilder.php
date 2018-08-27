<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

use Doctrine\RST\Parser as RSTParser;
use Parsedown;
use Symfony\Component\Filesystem\Filesystem;

class SourceFileBuilder
{
    /** @var SourceFileRenderer */
    private $sourceFileRenderer;

    /** @var Filesystem */
    private $filesystem;

    /** @var Parsedown */
    private $parsedown;

    /** @var RSTParser */
    private $rstParser;

    public function __construct(
        SourceFileRenderer $sourceFileRenderer,
        Filesystem $filesystem,
        Parsedown $parsedown,
        RSTParser $rstParser
    ) {
        $this->sourceFileRenderer = $sourceFileRenderer;
        $this->filesystem         = $filesystem;
        $this->parsedown          = $parsedown;
        $this->rstParser          = $rstParser;
    }

    public function buildFile(SourceFile $sourceFile, string $buildDir) : void
    {
        $parsedFile = $this->parseFile($sourceFile);

        if ($sourceFile->isTwig()) {
            $parsedFile = $this->sourceFileRenderer->render(
                $sourceFile,
                $parsedFile
            );
        }

        $this->filesystem->dumpFile($sourceFile->getWritePath(), $parsedFile);
    }

    private function parseFile(SourceFile $sourceFile) : string
    {
        $contents = $sourceFile->getContents();

        if ($sourceFile->isMarkdown()) {
            return $this->parsedown->text($contents);
        }

        if ($sourceFile->isRestructuredText()) {
            return $this->rstParser->parse($contents)->render();
        }

        return $contents;
    }
}
