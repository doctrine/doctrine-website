<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\RawNode;
use Gregwar\RST\SubDirective;
use Gregwar\RST\Parser;

class TocHeaderDirective extends SubDirective
{
    public function getName()
    {
        return 'tocheader';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new RawNode('<h2 class="toc-header">'.$data.'</h2>');
    }
}
