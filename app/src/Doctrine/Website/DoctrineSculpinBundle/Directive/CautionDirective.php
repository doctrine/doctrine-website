<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class CautionDirective extends SubDirective
{
    public function getName()
    {
        return 'caution';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="alert caution bg-light-yellow text-dark border"><i class="fas fa-exclamation-triangle text-danger mr-2"></i>', '</div>');
    }
}
