<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class NoticeDirective extends SubDirective
{
    public function getName() : string
    {
        return 'notice';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string[] $options
     */
    public function processSub(Parser $parser, $document, $variable, $data, array $options) : WrapperNode
    {
        return new WrapperNode($document, '<div class="alert notice bg-secondary text-white"><i class="fas fa-bell mr-2"></i>', '</div>');
    }
}
