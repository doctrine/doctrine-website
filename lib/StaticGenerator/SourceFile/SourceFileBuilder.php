<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use Symfony\Component\Filesystem\Filesystem;

use function preg_match;

class SourceFileBuilder
{
    /** @var SourceFileConverter[] */
    private array $converters;

    /**
     * @param SourceFileConverter[] $converters
     * @param string[]              $nonRenderablePatterns
     */
    public function __construct(
        private SourceFileRenderer $sourceFileRenderer,
        private Filesystem $filesystem,
        array $converters,
        private array $nonRenderablePatterns = [],
    ) {
        foreach ($converters as $converter) {
            foreach ($converter->getExtensions() as $extension) {
                $this->converters[$extension] = $converter;
            }
        }
    }

    public function buildFile(SourceFile $sourceFile): void
    {
        $renderedFile = $this->convertSourceFile($sourceFile);

        if ($this->isSourceFileRenderable($sourceFile)) {
            $renderedFile = $this->sourceFileRenderer->render(
                $sourceFile,
                $renderedFile,
            );
        }

        $this->filesystem->dumpFile($sourceFile->getParameter('writePath'), $renderedFile);
    }

    private function convertSourceFile(SourceFile $sourceFile): string
    {
        $extension = $sourceFile->getExtension();

        if (isset($this->converters[$extension])) {
            return $this->converters[$extension]->convertSourceFile($sourceFile);
        }

        return $sourceFile->getContents();
    }

    private function isSourceFileRenderable(SourceFile $sourceFile): bool
    {
        if (! $sourceFile->isTwig()) {
            return false;
        }

        foreach ($this->nonRenderablePatterns as $nonRenderablePattern) {
            if (preg_match($nonRenderablePattern, $sourceFile->getSourcePath()) > 0) {
                return false;
            }
        }

        return true;
    }
}
