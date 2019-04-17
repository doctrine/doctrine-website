<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Directives\Directive;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\QuoteNode;
use Doctrine\RST\Parser;
use function sprintf;

class VersionAddedDirective extends Directive
{
    public function getName() : string
    {
        return 'versionadded';
    }

    /**
     * @param string[] $options
     */
    public function process(
        Parser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ) : void {
        $document = $parser->getDocument();

        $renderedNode = '';

        if ($node !== null) {
            $renderedNode = $node->render();
        }

        $nodeFactory = $parser->getNodeFactory();

        if ($node instanceof QuoteNode) {
            $rawNode = $nodeFactory->createRawNode(sprintf('<div class="alert version-added bg-info text-white"><p class="new-version font-weight-bold"><i class="fas fa-plus-square"></i> New in version %s</p><p>%s</p></div>', $data, $renderedNode));
        } else {
            $rawNode = $nodeFactory->createRawNode(sprintf('<div class="alert version-added bg-info text-white"><p class="new-version font-weight-bold"><i class="fas fa-plus-square"></i> New in version %s</p></div><p>%s</p>', $data, $renderedNode));
        }

        $document->addNode($rawNode);
    }
}
