<?php

namespace Doctrine\Website\SculpinRstBundle\Node;

use Gregwar\RST\Nodes\Node;
use Twig_Environment as Twig;

class ContentsNode extends Node
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var string
     */
    public $header;

    /**
     * @var int
     */
    public $depth;

    /**
     * @var array[]
     */
    public $titles;

    public function __construct(Twig $twig, $header, $depth)
    {
        $this->twig = $twig;
        $this->header = $header;
        $this->depth = $depth;
        $this->titles = [];
    }

    public function render()
    {
        return $this->twig->render(
            '@SculpinRstBundle/Directive/contents.html.twig',
            [
                'header' => $this->header,
                'titles' => $this->titles,
            ]
        );
    }
}
