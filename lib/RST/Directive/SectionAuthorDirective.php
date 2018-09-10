<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Directive;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\RawNode;
use Doctrine\RST\Parser;
use function count;
use function preg_match;
use function sprintf;

class SectionAuthorDirective extends Directive
{
    public function getName() : string
    {
        return 'sectionauthor';
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
        preg_match('/(.*) <(.*)>/', $data, $match);

        if (count($match) === 3) {
            $name  = $match[1];
            $email = $match[2];
        } else {
            $name  = $data;
            $email = '';
        }

        if ($email !== '') {
            $nameHtml = sprintf('<a href="mailto:%s">%s</a>', $email, $name);
        } else {
            $nameHtml = $name;
        }

        $document = $parser->getDocument();

        $rawNode = new RawNode(sprintf('<div class="alert section-author bg-light text-dark border"><p class="author font-weight-bold"><i class="fas fa-pencil-alt"></i> Written by %s</p></div>', $nameHtml));

        $document->addNode($rawNode);
    }
}
