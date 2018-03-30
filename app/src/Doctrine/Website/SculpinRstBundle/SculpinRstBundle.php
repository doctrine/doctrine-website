<?php

namespace Doctrine\Website\SculpinRstBundle;

use Doctrine\Website\SculpinRstBundle\DependencyInjection\CompilerPass\DirectivesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SculpinRstBundle extends Bundle
{
    const CONVERTER_NAME = 'rst';

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DirectivesCompilerPass());
    }
}
