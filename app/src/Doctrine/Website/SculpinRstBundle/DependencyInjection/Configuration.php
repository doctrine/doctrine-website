<?php

namespace Doctrine\Website\SculpinRstBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;

        $rootNode = $treeBuilder->root('sculpin_rst');

        $rootNode
            ->children()
                ->arrayNode('extensions')
                    ->info('Determines which files Sculpin will pass into the reStructuredText convert.')
                    ->defaultValue(['rst'])
                    ->prototype('scalar')->end()
                ->end()
                ->booleanNode('demote_headings')
                    ->info('When true, demotes headings one level (h1 becomes a h2, et cetera)')
                    ->defaultFalse()
                ->end()
                ->scalarNode('directive_domain')
                    ->info('Determines the domain for the directives in this bundle')
                    ->defaultValue('')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
