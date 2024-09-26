<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\Compiler;

use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\Menu\GlobMenuEntryNode;
use phpDocumentor\Guides\Nodes\Node;

/**
 * This class will fix the glob menu entries.
 *
 * The glob menu entries are not correctly added in the docs.
 * This transformer will fix the glob menu entries by replacing the '*' with '**'. This add a certain
 * risk if the user had the intention to match only documents in the current directory. But this can be solved
 * adding a `/` at the beginning of the glob pattern.
 *
 * @implements NodeTransformer<GlobMenuEntryNode>
 */
final class GlobMenuFixerTransformer implements NodeTransformer
{
    public function enterNode(Node $node, CompilerContext $compilerContext): Node
    {
        return $node;
    }

    public function leaveNode(Node $node, CompilerContext $compilerContext): Node|null
    {
        if ($node->getUrl() === '*') {
            return new GlobMenuEntryNode(
                '**',
                $node->getLevel(),
            );
        }

        return $node;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof GlobMenuEntryNode;
    }

    public function getPriority(): int
    {
        // Before GlobMenuEntryNodeTransformer
        return 4001;
    }
}
