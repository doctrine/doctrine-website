<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\WrapperNode;
use Doctrine\RST\Parser;
use Doctrine\RST\SubDirective;

class NoticeDirective extends SubDirective
{
    public function getName() : string
    {
        return 'notice';
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
        return new WrapperNode($document, '<div class="alert notice bg-secondary text-white"><i class="fas fa-bell mr-2"></i>', '</div>');
    }
}
