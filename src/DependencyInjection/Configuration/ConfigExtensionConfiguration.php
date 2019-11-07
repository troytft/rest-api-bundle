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
        $treeBuilder = new TreeBuilder($this->alias);
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('mapper')
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
                    ->end()
                ->end()
                ->booleanNode('handle_request_model_mapping_exception')
                    ->defaultTrue()
                ->end()
             ->end();

        return $treeBuilder;
    }
}
