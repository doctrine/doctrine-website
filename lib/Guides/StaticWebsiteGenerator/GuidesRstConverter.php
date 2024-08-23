<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\StaticWebsiteGenerator;

use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFile;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileConverter;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use Override;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\RenderContext;

final readonly class GuidesRstConverter implements SourceFileConverter
{
    /** @param NodeRenderer<DocumentNode> $nodeRenderer */
    public function __construct(
        private Parser $parser,
        private NodeRenderer $nodeRenderer,
    ) {
    }

    /** @inheritDoc */
    #[Override]
    public function getExtensions(): array
    {
        return ['rst'];
    }

    #[Override]
    public function convertSourceFile(SourceFile $sourceFile): string
    {
        $document = $this->parser->parse($sourceFile->getContents(), 'rst');

        $renderContext = RenderContext::forDocument(
            $document,
            [$document],
            new Filesystem(new NullAdapter()),
            new Filesystem(new NullAdapter()),
            '/',
            'html',
            new ProjectNode(),
        );

        return $this->nodeRenderer->render($document, $renderContext);
    }
}
