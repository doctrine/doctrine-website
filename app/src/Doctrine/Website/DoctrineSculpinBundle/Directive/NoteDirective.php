<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class NoteDirective extends SubDirective
{
    public function getName()
    {
        return 'note';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="alert note bg-light-yellow text-dark border"><i class="fas fa-sticky-note text-primary mr-2"></i>', '</div>');
    }
}
