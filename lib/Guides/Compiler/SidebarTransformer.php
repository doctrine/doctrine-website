<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\Compiler;

use Doctrine\RST\Nodes\TocNode;
use Override;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Nodes\GeneralDirectiveNode;

final class SidebarTransformer implements NodeTransformer
{
    #[Override]
    public function enterNode(Node $node, CompilerContext $compilerContext): Node
    {
        return $node;
    }

    #[Override]
    public function leaveNode(Node $node, CompilerContext $compilerContext): Node|null
    {
        $nodes   = $compilerContext->getDocumentNode()->getDocumentPartNodes()['sidebar'] ?? [];
        $nodes[] = $node;

        $compilerContext->getDocumentNode()->addDocumentPart('sidebar', $nodes);

        return null;
    }

    #[Override]
    public function supports(Node $node): bool
    {
        if ($node instanceof GeneralDirectiveNode) {
            return $node->getName() === 'toc';
        }

        return $node instanceof TocNode;
    }

    #[Override]
    public function getPriority(): int
    {
        return 3000;
    }
}
