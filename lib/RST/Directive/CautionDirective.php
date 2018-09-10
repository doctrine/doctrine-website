<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class CautionDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('caution', 'bg-light-yellow', 'text-dark', 'fas fa-exclamation-triangle text-danger');
    }
}
