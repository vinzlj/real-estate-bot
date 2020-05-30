<?php

declare(strict_types=1);

namespace Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AppConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('app');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->scalarNode('database_path')->end()
                ->arrayNode('crawlers')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('base_url')->end()
                            ->scalarNode('ad_selector')->end()
                            ->arrayNode('urls')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
