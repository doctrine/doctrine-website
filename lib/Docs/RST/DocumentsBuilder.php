<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use phpDocumentor\Guides\Nodes\DocumentNode;

interface DocumentsBuilder
{
    public function build(string $directory, string $targetDirectory = 'output'): void;

    /** @return DocumentNode[] */
    public function getDocuments(): array;
}
