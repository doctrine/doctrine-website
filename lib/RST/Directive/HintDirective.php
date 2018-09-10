<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class HintDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('hint', 'bg-primary', 'text-white', 'fas fa-hand-point-right');
    }
}
