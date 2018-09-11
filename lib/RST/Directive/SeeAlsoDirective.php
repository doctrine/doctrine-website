<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class SeeAlsoDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('seealso', 'bg-light', 'text-dark', 'far fa-flag');
    }
}
