<?php

namespace Doctrine\Website\SculpinRstBundle\Directive;

use Gregwar\RST\Directive;
use Gregwar\RST\Document;
use Gregwar\RST\Nodes\TitleNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;
use ReflectionProperty;
use Doctrine\Website\SculpinRstBundle\Node\ContentsNode;
use Doctrine\Website\SculpinRstBundle\Node\FigureNode;
use Twig_Environment as Twig;

class FigureDirective extends SubDirective
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var string
     */
    private $directiveDomain;

    /**
     * @param Twig $twig
     * @param string $directiveDomain
     */
    public function __construct(Twig $twig, $directiveDomain)
    {
        $this->twig = $twig;
        $this->directiveDomain = $directiveDomain;
    }

    public function getName()
    {
        return ($this->directiveDomain ? "$this->directiveDomain:" : '') . 'figure';
    }

    public function processSub(Parser $parser, $document, $variable, $data, array $options)
    {
        $options += [
            'class' => null,
            'alt' => null,
            'title' => null,
            'source' => null
        ];

        if (preg_match('~^(.+)\s+\\((.+)\\)$~', $options['source'], $matches)) {
            $sourceTitle = $matches[1];
            $sourceUrl = $matches[2];
        } else {
            $sourceTitle = null;
            $sourceUrl = (string) $options['source'];
        }

        return new FigureNode(
            $this->twig,
            (string) $data,
            $document->render(),
            (string) $options['class'] ?: null,
            (string) $options['alt'] ?: null,
            (string) $options['title'] ?: null,
            $sourceTitle,
            $sourceUrl
        );
    }

    public function finalize(Document &$document)
    {
        $titles = array_filter(
            $document->getNodes(),
            function ($node) {
                return $node instanceof TitleNode;
            }
        );

        foreach ($document->getNodes() as $node) {
            if ($node instanceof ContentsNode) {
                $this->addTitles($node, $titles);
            }
        }
    }

    /**
     * @param \Doctrine\Website\SculpinRstBundle\Node\ContentsNode $contents
     * @param TitleNode[] $titles
     */
    private function addTitles(ContentsNode $contents, array $titles)
    {
        $depth = $contents->depth;
        $titles = array_filter(
            $titles,
            function (TitleNode $title) use ($depth) {
                return $title->getLevel() <= $depth;
            }
        );

        $tokenReflection = new ReflectionProperty(TitleNode::class, 'token');
        $tokenReflection->setAccessible(true);

        $titles = array_map(
            function (TitleNode $title) use ($tokenReflection) {
                return [
                    'level'  => $title->getLevel(),
                    'token'  => $tokenReflection->getValue($title),
                    'text' => $title->getValue()
                ];
            },
            $titles
        );

        $contents->titles = $titles;
    }
}
