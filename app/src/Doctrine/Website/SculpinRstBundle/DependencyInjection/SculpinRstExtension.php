<?php

namespace Doctrine\Website\SculpinRstBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SculpinRstExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration;
        $config = $this->processConfiguration($configuration, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('sculpin_rst.extensions', $config['extensions']);

        $container
            ->getDefinition('sculpin_rst.twig.loader')
            ->addMethodCall(
                'setPaths',
                [
                    array_filter(
                        [
                            __DIR__ . '/../Resources/views',
                            $container->getParameter('kernel.root_dir') . '/Resources/SculpinRstBundle/views',
                        ],
                        'file_exists'
                    ),
                    'SculpinRstBundle'
                ]
            );

        if ($config['demote_headings']) {
            $container
                ->getDefinition('sculpin_rst.listener.demote_headings')
                ->addTag('kernel.event_subscriber');
        }

        $container->setParameter('sculpin_rst.directive_domain', $config['directive_domain']);
    }
}
