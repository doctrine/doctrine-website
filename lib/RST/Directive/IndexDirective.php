<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Directives\SubDirective;

class IndexDirective extends SubDirective
{
    public function getName(): string
    {
        return 'index';
    }
}
