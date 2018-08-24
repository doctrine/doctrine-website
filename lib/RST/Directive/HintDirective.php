<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\WrapperNode;
use Doctrine\RST\Parser;
use Doctrine\RST\SubDirective;

class HintDirective extends SubDirective
{
    public function getName() : string
    {
        return 'hint';
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        return new WrapperNode($document, '<div class="alert hint bg-primary text-white"><i class="fas fa-hand-point-right mr-2"></i>', '</div>');
    }
}
