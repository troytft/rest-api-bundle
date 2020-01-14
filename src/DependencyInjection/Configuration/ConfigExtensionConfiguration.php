<?php

namespace RestApiBundle\DependencyInjection\Configuration;

use RestApiBundle;
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
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_REQUEST_PROPERTIES_NULLABLE_BY_DEFAULT)
                    ->defaultFalse()
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_REQUEST_UNDEFINED_KEYS_ALLOWED)
                    ->defaultFalse()
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_REQUEST_CLEAR_MISSING_ENABLED)
                    ->defaultTrue()
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED)
                    ->defaultTrue()
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE)
                    ->defaultTrue()
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATE_FORMAT)
                    ->defaultValue('Y-m-d')
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATETIME_FORMAT)
                    ->defaultValue('Y-m-d\TH:i:sP')
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_RESPONSE_HANDLER_ENABLED)->end()
            ->end();

        return $treeBuilder;
    }
}
