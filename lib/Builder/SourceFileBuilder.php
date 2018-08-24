<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

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

    public function __construct(
        SourceFileRenderer $sourceFileRenderer,
        Filesystem $filesystem,
        Parsedown $parsedown
    ) {
        $this->sourceFileRenderer = $sourceFileRenderer;
        $this->filesystem         = $filesystem;
        $this->parsedown          = $parsedown;
    }

    public function buildFile(SourceFile $sourceFile, string $buildDir) : void
    {
        $rendered = $sourceFile->getContents();

        if ($sourceFile->isMarkdown()) {
            $rendered = $this->parsedown->text($rendered);
        }

        if ($sourceFile->isTwig()) {
            $rendered = $this->sourceFileRenderer->render(
                $sourceFile,
                $rendered
            );
        }

        $this->filesystem->dumpFile($sourceFile->getWritePath(), $rendered);
    }
}
