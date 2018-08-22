<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\CodeNode;
use Gregwar\RST\Nodes\RawNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;
use function strtoupper;

class ConfigurationBlockDirective extends SubDirective
{
    public function getName() : string
    {
        return 'configuration-block';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string[] $options
     */
    public function processSub(Parser $parser, $document, $variable, $data, array $options) : RawNode
    {
        $html = '<div class="configuration-block jsactive clearfix"><ul class="simple">';

        foreach ($document->getNodes() as $node) {
            if (! $node instanceof CodeNode) {
                continue;
            }

            $language = $node->getLanguage() ?? 'Unknown';

            $html .= '<li>';
            $html .= '<em>' . strtoupper($language) . '</em>';
            $html .= '<div class="highlight-' . $language . '">' . $node->render() . '</div>';
            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';

        return new RawNode($html);
    }
}
