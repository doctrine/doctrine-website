<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class NoticeDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('notice', 'bg-secondary', 'text-white', 'fas fa-bell');
    }
}
