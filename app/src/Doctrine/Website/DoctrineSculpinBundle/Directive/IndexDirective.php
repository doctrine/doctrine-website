<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Directive;

use Gregwar\RST\SubDirective;

class IndexDirective extends SubDirective
{
    public function getName() : string
    {
        return 'index';
    }
}
