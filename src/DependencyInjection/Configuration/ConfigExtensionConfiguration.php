<?php

namespace RestApiBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigExtensionConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('rest_api')
            ->children()
                ->arrayNode('request_model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('nullable_by_default')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('allow_undefined_keys')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('clear_missing')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('handle_mapping_exception')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()

             ->end();

        return $treeBuilder;
    }
}
