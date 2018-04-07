<?php

namespace Doctrine\Website\DoctrineSculpinBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DirectivesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $kernel = $container->getDefinition('doctrine.rst.kernel');
        $kernel->replaceArgument(
            1,
            array_map(
                [$container, 'getDefinition'],
                array_keys($container->findTaggedServiceIds('doctrine.rst.directive'))
            )
        );
    }
}
