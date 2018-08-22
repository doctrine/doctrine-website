<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class WarningDirective extends SubDirective
{
    public function getName() : string
    {
        return 'warning';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string[] $options
     */
    public function processSub(Parser $parser, $document, $variable, $data, array $options) : WrapperNode
    {
        return new WrapperNode($document, '<div class="alert warning bg-light-yellow text-dark border"><i class="fas fa-exclamation-circle text-warning mr-2"></i>', '</div>');
    }
}
