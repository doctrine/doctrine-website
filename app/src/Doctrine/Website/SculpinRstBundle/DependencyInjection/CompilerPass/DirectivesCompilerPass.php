<?php

namespace Doctrine\Website\SculpinRstBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DirectivesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $kernel = $container->getDefinition('sculpin_rst.kernel.sculpin');
        $kernel->replaceArgument(
            1,
            array_map(
                [$container, 'getDefinition'],
                array_keys($container->findTaggedServiceIds('sculpin.rst.directive'))
            )
        );
    }
}
