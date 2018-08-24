<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\RawNode;
use Doctrine\RST\Parser;
use Doctrine\RST\SubDirective;

class TocHeaderDirective extends SubDirective
{
    public function getName() : string
    {
        return 'tocheader';
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
        return new RawNode('<h2 class="toc-header">' . $data . '</h2>');
    }
}
