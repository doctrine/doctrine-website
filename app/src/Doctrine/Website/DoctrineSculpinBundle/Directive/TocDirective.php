<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\SubDirective;
use Gregwar\RST\Parser;

class TocDirective extends SubDirective
{
    public function getName()
    {
        return 'toc';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="toc-section">', '</div>');
    }
}
