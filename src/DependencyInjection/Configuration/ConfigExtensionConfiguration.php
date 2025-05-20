<?php
declare(strict_types=1);

namespace RestApiBundle\DependencyInjection\Configuration;

use RestApiBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function constant;
use function defined;
use function is_int;

class ConfigExtensionConfiguration implements ConfigurationInterface
{
    private string $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $treeBuilder
            ->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_REQUEST_VALIDATION_EXCEPTION_HANDLER_ENABLED)
                    ->defaultTrue()
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_FORCE_REQUEST_DATETIME_TO_LOCAL_TIMEZONE)
                    ->defaultTrue()
                ->end()
                ->scalarNode(RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATE_FORMAT)
                    ->defaultValue('Y-m-d')
                ->end()
                ->scalarNode(RestApiBundle\Enum\SettingsKey::RESPONSE_MODEL_DATE_FORMAT)
                    ->defaultValue('Y-m-d')
                ->end()
                ->scalarNode(RestApiBundle\Enum\SettingsKey::DEFAULT_REQUEST_DATETIME_FORMAT)
                    ->defaultValue('Y-m-d\TH:i:sP')
                ->end()
                ->booleanNode(RestApiBundle\Enum\SettingsKey::IS_RESPONSE_HANDLER_ENABLED)
                    ->defaultTrue()
                ->end()
                ->scalarNode(RestApiBundle\Enum\SettingsKey::RESPONSE_JSON_ENCODE_OPTIONS)
                    ->defaultValue(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    ->beforeNormalization()
                    ->ifArray()->then(function ($options) {
                        $result = 0;
                        foreach ($options as $option) {
                            if (is_int($option)) {
                                $result |= (int) $option;
                            } elseif (defined($option)) {
                                $result |= constant($option);
                            } else {
                                throw new \InvalidArgumentException('Expected either an integer representing one of the JSON_ constants, or a string of the constant itself.');
                            }
                        }

                        return $result;
                    })
                    ->end()
                    ->beforeNormalization()
                    ->ifString()->then(function ($options) {
                        if (is_int($options)) {
                            $result = (int) $options;
                        } elseif (defined($options)) {
                            $result = constant($options);
                        } else {
                            throw new \InvalidArgumentException('Expected either an integer representing one of the JSON_ constants, or a string of the constant itself.');
                        }

                        return $result;
                    })
                    ->end()
                    ->validate()
                    ->always(function ($v) {
                        if (!is_int($v)) {
                            throw new \InvalidArgumentException('Expected either integer value or a array of the JSON_ constants.');
                        }

                        return $v;
                    })
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
