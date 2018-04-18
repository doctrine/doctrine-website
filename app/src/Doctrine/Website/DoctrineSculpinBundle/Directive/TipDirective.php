<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class TipDirective extends SubDirective
{
    public function getName()
    {
        return 'tip';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="alert tip bg-success text-light"><i class="fas fa-question-circle mr-2"></i>', '</div>');
    }
}
