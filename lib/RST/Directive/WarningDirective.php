<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class WarningDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('warning', 'bg-light-yellow', 'text-dark', 'fas fa-exclamation-circle text-warning');
    }
}
