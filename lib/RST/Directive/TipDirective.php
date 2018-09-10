<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class TipDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('tip', 'bg-success', 'text-light', 'fas fa-question-circle');
    }
}
