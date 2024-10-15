<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\ReferenceResolver;

use phpDocumentor\Guides\Nodes\Inline\LinkInlineNode;
use phpDocumentor\Guides\ReferenceResolvers\Messages;
use phpDocumentor\Guides\ReferenceResolvers\ReferenceResolver;
use phpDocumentor\Guides\RenderContext;

use function str_ends_with;

class HtmlResolver implements ReferenceResolver
{
    public function resolve(LinkInlineNode $node, RenderContext $renderContext, Messages $messages): bool
    {
        if (str_ends_with($node->getTargetReference(), '.html')) {
            $node->setUrl($node->getTargetReference());

            return true;
        }

        return false;
    }

    public static function getPriority(): int
    {
        return -300;
    }
}
