<?php

declare(strict_types=1);

namespace Doctrine\Website\RST;

use Doctrine\RST\Builder;
use Doctrine\RST\Directive;
use Doctrine\RST\Document;
use Doctrine\RST\Factory;
use Doctrine\RST\HTML\Kernel as HtmlKernel;
use Doctrine\RST\Kernel as BaseKernel;
use Doctrine\RST\Reference;
use function array_merge;

class Kernel extends BaseKernel
{
    /** @var HtmlKernel */
    private $baseKernel;

    /** @var Directive[] */
    private $directives;

    /**
     * @param Directive[] $directives
     */
    public function __construct(HtmlKernel $baseKernel, array $directives)
    {
        $this->baseKernel = $baseKernel;
        $this->directives = $directives;

        parent::__construct($directives);
    }

    public function getName() : string
    {
        return 'doctrine';
    }

    /**
     * @return Directive[]
     */
    public function getDirectives() : array
    {
        return array_merge($this->baseKernel->getDirectives(), $this->directives);
    }

    public function getFileExtension() : string
    {
        return $this->baseKernel->getFileExtension();
    }

    public function getFactory() : Factory
    {
        return $this->baseKernel->getFactory();
    }

    /**
     * @return Reference[]
     */
    public function getReferences() : array
    {
        return $this->baseKernel->getReferences();
    }

    public function postParse(Document $document) : void
    {
        $this->baseKernel->postParse($document);
    }

    public function initBuilder(Builder $builder) : void
    {
        $this->baseKernel->initBuilder($builder);
    }
}
