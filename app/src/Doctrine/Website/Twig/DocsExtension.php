<?php

namespace Doctrine\Website\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleTest;

class DocsExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [];
    }
}
