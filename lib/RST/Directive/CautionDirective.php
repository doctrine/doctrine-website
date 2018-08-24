<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\WrapperNode;
use Doctrine\RST\Parser;
use Doctrine\RST\SubDirective;

class CautionDirective extends SubDirective
{
    public function getName() : string
    {
        return 'caution';
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
        return new WrapperNode($document, '<div class="alert caution bg-light-yellow text-dark border"><i class="fas fa-exclamation-triangle text-danger mr-2"></i>', '</div>');
    }
}
