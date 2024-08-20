<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\RST\Builder;
use Override;
use phpDocumentor\Guides\Nodes\DocumentNode;

final class DoctrineRSTBuilder implements DocumentsBuilder
{
    public function __construct(
        private Builder $builder,
    ) {
    }

    #[Override]
    public function build(string $directory, string $targetDirectory = 'output'): void
    {
        // we have to get a fresh builder due to how the RST parser works
        $this->builder = $this->builder->recreate();

        // build the docs from the files in $docsDir and write them to $outputPath
        // which is contained inside the $sourceDir
        $this->builder->build(
            $directory,
            $targetDirectory,
        );
    }

    /** @return DocumentNode[] */
    #[Override]
    public function getDocuments(): array
    {
        return $this->builder->getDocuments()->getAll();
    }
}
