<?php

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
       $rootNode = $treeBuilder->root('netvlies_doctrinestoragebridge', 'array');
//
//        $rootNode
//            ->children()
//                ->arrayNode('templates')
//                ->addDefaultsIfNotSet()
//                    ->children()
//                        ->scalarNode('user_block')->defaultValue('SonataAdminBundle:Core:user_block.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('layout')->defaultValue('NetvliesOmsBundle:Sonata:Admin/base_layout.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('ajax')->defaultValue('SonataAdminBundle::ajax_layout.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('dashboard')->defaultValue('SonataAdminBundle:Core:dashboard.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('list')->defaultValue('SonataAdminBundle:CRUD:list.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('show')->defaultValue('SonataAdminBundle:CRUD:show.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('edit')->defaultValue('SonataAdminBundle:CRUD:edit.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('history')->defaultValue('SonataAdminBundle:CRUD:history.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('history_revision')->defaultValue('SonataAdminBundle:CRUD:history_revision.html.twig')->cannotBeEmpty()->end()
//                        ->scalarNode('action')->defaultValue('SonataAdminBundle:CRUD:action.html.twig')->cannotBeEmpty()->end()
//                    ->end()
//                ->end()
//                ->arrayNode('overwrites')
//                ->addDefaultsIfNotSet()
//                    ->children()
//                        ->scalarNode('sonata')->defaultValue(true)->end()
//                        ->scalarNode('knp')->defaultValue(true)->end()
//                    ->end()
//                ->end()
//                ->scalarNode('sonata_admin_module_class')->end()
//            ->end()
//        ->end();

        return $treeBuilder;
    }
}
