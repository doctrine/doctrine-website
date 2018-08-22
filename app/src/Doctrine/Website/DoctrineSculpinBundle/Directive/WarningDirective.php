<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\WrapperNode;
use Doctrine\RST\Parser;
use Doctrine\RST\SubDirective;

class WarningDirective extends SubDirective
{
    public function getName() : string
    {
        return 'warning';
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
        return new WrapperNode($document, '<div class="alert warning bg-light-yellow text-dark border"><i class="fas fa-exclamation-circle text-warning mr-2"></i>', '</div>');
    }
}
