<?php

declare(strict_types=1);

namespace Doctrine\Website\RST;

use Gregwar\RST\Builder;
use Gregwar\RST\Directive;
use Gregwar\RST\Document;
use Gregwar\RST\HTML\Kernel as HtmlKernel;
use Gregwar\RST\Kernel as BaseKernel;
use function array_merge;

class Kernel extends BaseKernel
{
    /** @var HtmlKernel */
    private $baseKernel;

    /** @var Directive[] */
    private $directives;

    public function __construct(HtmlKernel $baseKernel, array $directives)
    {
        $this->baseKernel = $baseKernel;
        $this->directives = $directives;
    }

    public function getName()
    {
        return 'doctrine';
    }

    public function getDirectives()
    {
        return array_merge($this->baseKernel->getDirectives(), $this->directives);
    }

    public function getFileExtension()
    {
        return $this->baseKernel->getFileExtension();
    }

    public function getClass($name)
    {
        return $this->baseKernel->getClass($name);
    }

    public function getReferences()
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
