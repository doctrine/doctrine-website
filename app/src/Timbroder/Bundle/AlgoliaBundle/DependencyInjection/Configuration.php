<?php
/**
 * Created by PhpStorm.
 * User: tbroder
 * Date: 10/17/15
 * Time: 1:49 PM
 */

namespace Timbroder\Bundle\AlgoliaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 *
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 * @author Tim Broder <timothy.broder@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('timbroder_algolia');

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                ->end()
                ->scalarNode('engine')
                    ->validate()
                    ->ifNotInArray(array('algolia'))
                        ->thenInvalid('Invalid search engine : "%s"')
                    ->end()
                    ->defaultValue('algolia')
                ->end()
                ->arrayNode('options')
                    ->isRequired()
                    ->children()
                        ->scalarNode('application_id')->isRequired()->end()
                        ->scalarNode('admin_api_key')->isRequired()->end()
                        ->scalarNode('index')->isRequired()->end()
                        ->scalarNode('clear_on_sync')->end()
                    ->end()
                ->end()
            ->end();
        ;

        return $treeBuilder;
    }
}
