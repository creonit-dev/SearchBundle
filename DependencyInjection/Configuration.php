<?php

namespace Creonit\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('creonit_search');

        $rootNode
            ->children()
                ->arrayNode('sphinx')
                    ->children()
                        ->scalarNode('port')->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('path')->end()
                    ->end()
                ->end()
                ->arrayNode('database')
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('user')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('dbname')->end()
                    ->end()
                ->end()
                ->variableNode('indexes')->end()
            ->end();

        return $treeBuilder;
    }
}
