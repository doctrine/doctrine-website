<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Directives\SubDirective;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Nodes\WrapperNode;
use Doctrine\RST\Parser;

use function sprintf;

class AdmonitionDirective extends SubDirective
{
    public function __construct(private string $name, private string $backgroundColor, private string $textColor, private string $icon)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @param string[] $options */
    public function processSub(
        Parser $parser,
        Node|null $document,
        string $variable,
        string $data,
        array $options,
    ): Node|null {
        return new WrapperNode($document, sprintf('<div class="alert %s-admonition %s %s border"><table width="100%%"><tr><td width="10" class="align-top"><i class="%s mr-2"></i></td><td>', $this->name, $this->backgroundColor, $this->textColor, $this->icon), '</td></tr></table></div>');
    }
}
