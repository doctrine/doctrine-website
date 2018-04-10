<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\CodeNode;
use Gregwar\RST\Nodes\RawNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class ConfigurationBlockDirective extends SubDirective
{
    public function getName()
    {
        return 'configuration-block';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        $html = '<div class="configuration-block jsactive clearfix"><ul class="simple">';

        foreach ($document->getNodes() as $node) {
            if (!$node instanceof CodeNode) {
                continue;
            }

            $html .= '<li>';
            $html .= '<em>'.strtoupper($node->getLanguage()).'</em>';
            $html .= '<div class="highlight-'.$node->getLanguage().'">'.$node->render().'</div>';
            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';

        return new RawNode($html);
    }
}
