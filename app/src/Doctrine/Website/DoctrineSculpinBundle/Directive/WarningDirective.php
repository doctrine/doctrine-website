<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class WarningDirective extends SubDirective
{
    public function getName()
    {
        return 'warning';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="alert warning bg-light-yellow text-dark border"><i class="fas fa-exclamation-circle text-warning mr-2"></i>', '</div>');
    }
}
