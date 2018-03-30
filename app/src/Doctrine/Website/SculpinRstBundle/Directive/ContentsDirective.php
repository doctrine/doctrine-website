<?php

namespace Doctrine\Website\SculpinRstBundle\Directive;

use Gregwar\RST\Directive;
use Gregwar\RST\Document;
use Gregwar\RST\Nodes\TitleNode;
use Gregwar\RST\Parser;
use ReflectionProperty;
use Doctrine\Website\SculpinRstBundle\Node\ContentsNode;
use Twig_Environment as Twig;

class ContentsDirective extends Directive
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
        return ($this->directiveDomain ? "$this->directiveDomain:" : '') . 'contents';
    }

    public function processNode(Parser $parser, $variable, $data, array $options)
    {
        $header = $data ?: 'Contents';
        $options += [
            'depth' => 6,
        ];

        return new ContentsNode($this->twig, $header, $options['depth']);
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
