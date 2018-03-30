<?php

namespace Doctrine\Website\SculpinRstBundle\Node;

use Gregwar\RST\Nodes\Node;
use Twig_Environment as Twig;

class FigureNode extends Node
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string|null
     */
    public $caption;

    /**
     * @var string|null
     */
    public $class;

    /**
     * @var string|null
     */
    public $alt;

    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $sourceTitle;

    /**
     * @var string|null
     */
    public $sourceUrl;

    /**
     * @param Twig $twig
     * @param string $url
     * @param string|null $caption
     * @param string|null $class
     * @param string|null $alt
     * @param string|null $title
     * @param string|null $sourceTitle
     * @param string|null $sourceUrl
     */
    public function __construct($twig, $url, $caption, $class, $alt, $title, $sourceTitle, $sourceUrl)
    {
        $this->twig = $twig;
        $this->url = $url;
        $this->caption = $caption;
        $this->class = $class;
        $this->alt = $alt;
        $this->title = $title;
        $this->sourceTitle = $sourceTitle;
        $this->sourceUrl = $sourceUrl;
    }


    public function render()
    {
        return $this->twig->render(
            '@SculpinRstBundle/Directive/figure.html.twig',
            [
                'url' => $this->url,
                'caption' => $this->caption,
                'class' => $this->class,
                'alt' => $this->alt,
                'title' => $this->title,
                'sourceTitle' => $this->sourceTitle,
                'sourceUrl' => $this->sourceUrl,
            ]
        );
    }
}
