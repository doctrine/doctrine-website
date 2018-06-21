<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\RawNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class TocHeaderDirective extends SubDirective
{
    public function getName() : string
    {
        return 'tocheader';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string[] $options
     */
    public function processSub(Parser $parser, $document, $variable, $data, array $options) : RawNode
    {
        return new RawNode('<h2 class="toc-header">' . $data . '</h2>');
    }
}
