<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class SectionAuthorDirective extends SubDirective
{
    public function getName()
    {
        return 'sectionauthor';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<div class="section-author">', '</div>');
    }
}
