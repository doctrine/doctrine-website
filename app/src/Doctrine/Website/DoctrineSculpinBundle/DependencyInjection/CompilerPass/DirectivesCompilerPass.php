<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function array_keys;
use function array_map;

class DirectivesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
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
