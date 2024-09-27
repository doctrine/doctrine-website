<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\Compiler;

use Override;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Menu\NavMenuNode;
use phpDocumentor\Guides\Nodes\Menu\TocNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Nodes\GeneralDirectiveNode;

/**
 * This class will move the menu and toc nodes to the sidebar.
 *
 * As the toc node and menus should be displayed in the sidebar, this transformer will move them to a separate
 * documentPart. It will be removed from the main content of the document.
 *
 * @implements NodeTransformer<TocNode|GeneralDirectiveNode>
 */
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
        if ($compilerContext->getShadowTree()->getParent()?->getNode() instanceof DocumentNode === false) {
            return $node;
        }

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

        return $node instanceof NavMenuNode;
    }

    #[Override]
    public function getPriority(): int
    {
        return 1;
    }
}
