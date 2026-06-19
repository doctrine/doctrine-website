<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\Website\Model\ProjectVersion;
use phpDocumentor\Guides\Nodes\DocumentNode;

interface DocumentsBuilder
{
    public function build(ProjectVersion $version, string $directory, string $targetDirectory = 'output'): void;

    /** @return DocumentNode[] */
    public function getDocuments(): array;
}
