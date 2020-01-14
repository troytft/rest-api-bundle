<?php

namespace RestApiBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigExtensionConfiguration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root($this->alias)
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
                        ->booleanNode('handle_exception')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('date_time_transformer_force_local_timezone')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('date_time_transformer_default_format')
                            ->defaultValue('Y-m-d\TH:i:sP')
                        ->end()
                        ->scalarNode('date_transformer_default_format')
                            ->defaultValue('Y-m-d')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('response_model')
                    ->addDefaultsIfNotSet()
                    ->children()
                         ->booleanNode('is_response_subscriber_enabled')
                            ->defaultTrue()
                         ->end()
                    ->end()
                ->end()
             ->end();

        return $treeBuilder;
    }
}
