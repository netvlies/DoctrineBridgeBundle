<?php

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\Definition\Processor;

class NetvliesDoctrineStorageBridgeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //$processor = new Processor();
        //$configuration = new Configuration();
        //$config = $processor->processConfiguration($configuration, $configs);
        
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');


        // probably make this configurable...
        $cacheDirectory = '%kernel.cache_dir%/NetvliesDoctrineStorageBridge_annotation';
        $cacheDirectory = $container->getParameterBag()->resolveValue($cacheDirectory);
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        // the cache directory should be the first argument of the cache service
        $container
            ->getDefinition('netvliesDoctrineStorageBridge.metadata_cache')
            ->replaceArgument(0, $cacheDirectory)
        ;

    }
        
       

}
