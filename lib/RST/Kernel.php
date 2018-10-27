<?php

declare(strict_types=1);

namespace Doctrine\Website\RST;

use Doctrine\RST\Builder;
use Doctrine\RST\Configuration;
use Doctrine\RST\Directive;
use Doctrine\RST\Document;
use Doctrine\RST\Environment as BaseEnvironment;
use Doctrine\RST\HTML\Kernel as HtmlKernel;
use Doctrine\RST\Kernel as BaseKernel;
use Doctrine\RST\NodeFactory;
use Doctrine\RST\Reference;
use function array_merge;

class Kernel extends BaseKernel
{
    /** @var HtmlKernel */
    private $baseKernel;

    /** @var Directive[] */
    protected $directives;

    /**
     * @param Directive[] $directives
     */
    public function __construct(HtmlKernel $baseKernel, array $directives)
    {
        $this->baseKernel = $baseKernel;
        $this->directives = $directives;

        parent::__construct(null, $directives);
    }

    public function getName() : string
    {
        return 'doctrine';
    }

    /**
     * @return Directive[]
     */
    public function createDirectives() : array
    {
        return array_merge($this->baseKernel->createDirectives(), $this->directives);
    }

    public function createEnvironment(?Configuration $configuration = null) : BaseEnvironment
    {
        return $this->baseKernel->createEnvironment($configuration);
    }

    public function getFileExtension() : string
    {
        return $this->baseKernel->getFileExtension();
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

    protected function createNodeFactory() : NodeFactory
    {
        return $this->baseKernel->createNodeFactory();
    }
}
