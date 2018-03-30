<?php

namespace Timbroder\Bundle\AlgoliaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Sculpin Search Extension.
 *
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 * @author Tim Broder <timothy.broder@gmail.com>
 */
class TimbroderAlgoliaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration;
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('timbroder_sculpin.search.enabled', $config['enabled']);
        $container->setParameter('timbroder_sculpin.search.engine', $config['engine']);
        $container->setParameter('timbroder_sculpin.search.options.application_id', $config['options']['application_id']);
        $container->setParameter('timbroder_sculpin.search.options.admin_api_key', $config['options']['admin_api_key']);
        $container->setParameter('timbroder_sculpin.search.options.index', $config['options']['index']);
        $container->setParameter('timbroder_sculpin.search.options.clear_on_sync', $config['options']['clear_on_sync']);

        $referenceSearch = new Reference('timbroder_sculpin.search.engine.'.$config['engine']);
        $referenceBuilder = new Reference('timbroder_sculpin.search.document_builder.'.$config['engine']);
    }
}
