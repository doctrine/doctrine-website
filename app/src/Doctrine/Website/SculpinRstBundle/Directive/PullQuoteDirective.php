<?php

namespace Doctrine\Website\SculpinRstBundle\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

class PullQuoteDirective extends SubDirective
{
    /**
     * @var string
     */
    private $directiveDomain;

    /**
     * @param string $directiveDomain
     */
    public function __construct($directiveDomain)
    {
        $this->directiveDomain = $directiveDomain;
    }

    public function getName()
    {
        return ($this->directiveDomain ? "$this->directiveDomain:" : '') . 'pull-quote';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        return new WrapperNode($document, '<blockquote class="pull-quote">', '</blockquote>');
    }
}
