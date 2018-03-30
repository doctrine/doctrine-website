<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class NoticeDirective extends SubDirective
{
    public function getName()
    {
        return 'notice';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="alert notice bg-secondary text-white"><i class="fas fa-bell mr-2"></i>', '</div>');
    }
}
