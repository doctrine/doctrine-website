<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

class NoteDirective extends AdmonitionDirective
{
    public function __construct()
    {
        parent::__construct('note', 'bg-light-yellow', 'text-dark', 'fas fa-sticky-note text-primary');
    }
}
