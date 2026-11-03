<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\StaticWebsiteGenerator;

use Doctrine\Website\StaticGenerator\SourceFile\SourceFile;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileConverter;
use Override;
use phpDocumentor\FileSystem\FlySystemAdapter;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\RenderContext;

final readonly class GuidesMarkdownConverter implements SourceFileConverter
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
        return ['md', 'markdown'];
    }

    #[Override]
    public function convertSourceFile(SourceFile $sourceFile): string
    {
        $document = $this->parser->parse($sourceFile->getContents(), 'md');

        $renderContext = RenderContext::forDocument(
            $document,
            [$document],
            FlySystemAdapter::createInMemory(),
            FlySystemAdapter::createInMemory(),
            '/',
            'html',
            new ProjectNode(),
        );

        return $this->nodeRenderer->render($document, $renderContext);
    }
}
