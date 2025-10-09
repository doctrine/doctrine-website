<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use RuntimeException;
use Throwable;
use Twig\Error\SyntaxError;

use function sprintf;

class SourceFilesBuilder
{
    public function __construct(
        private SourceFileBuilder $sourceFileBuilder,
    ) {
    }

    /**
     * @param SourceFiles<SourceFile> $sourceFiles
     *
     * @throws RuntimeException
     */
    public function buildSourceFiles(SourceFiles $sourceFiles): void
    {
        foreach ($sourceFiles as $sourceFile) {
            try {
                $this->sourceFileBuilder->buildFile($sourceFile);
            } catch (SyntaxError $e) {
                throw new RuntimeException(message: sprintf(
                    <<<'EXCEPTION'
                    Failed building file "%s" from "%s", error on line %d:

                    "%s"

                    %s
                    EXCEPTION,
                    $sourceFile->getSourcePath(),
                    $e->getFile(),
                    $e->getTemplateLine(),
                    $e->getMessage(),
                    $e->getTraceAsString(),
                ), previous: $e);
            } catch (Throwable $e) {
                throw new RuntimeException(message: sprintf(
                    'Failed building file "%s" with error "%s',
                    $sourceFile->getSourcePath(),
                    $e->getMessage() . "\n\n" . $e->getTraceAsString(),
                ), previous: $e);
            }
        }
    }
}
