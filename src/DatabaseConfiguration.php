<?php

namespace Xoptov\BinancePlatform;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class DatabaseConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("database");

        $rootNode
            ->children()
                ->scalarNode("path")
                    ->defaultValue("platform.db")
                ->end()
            ->end();

        return $treeBuilder;
    }
}