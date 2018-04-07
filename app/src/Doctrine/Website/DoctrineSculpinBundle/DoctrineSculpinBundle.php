<?php

namespace Doctrine\Website\DoctrineSculpinBundle;

use Doctrine\Website\DoctrineSculpinBundle\DependencyInjection\CompilerPass\DirectivesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineSculpinBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DirectivesCompilerPass());
    }
}
