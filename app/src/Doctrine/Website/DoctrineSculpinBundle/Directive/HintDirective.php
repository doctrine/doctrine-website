<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class HintDirective extends SubDirective
{
    public function getName()
    {
        return 'hint';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="alert hint bg-primary text-white"><i class="fas fa-hand-point-right mr-2"></i>', '</div>');
    }
}
