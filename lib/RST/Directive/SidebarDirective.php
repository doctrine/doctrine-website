<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class SidebarDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('sidebar', 'bg-light', 'text-dark', 'fas fa-arrow-alt-circle-right');
    }
}
