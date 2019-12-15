<?php

namespace RestApiBundle\DependencyInjection;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ConfigExtension extends Extension
{
    public const PARAMETER_REQUEST_MODEL_NULLABLE_BY_DEFAULT = 'rest_api.request_model.nullable_by_default';
    public const PARAMETER_REQUEST_MODEL_ALLOW_UNDEFINED_KEYS = 'rest_api.request_model.allow_undefined_keys';
    public const PARAMETER_REQUEST_MODEL_CLEAR_MISSING = 'rest_api.request_model.clear_missing';
    public const PARAMETER_REQUEST_MODEL_HANDLE_EXCEPTION = 'rest_api.request_model.handle_exception';
    public const PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_FORCE_LOCAL_TIMEZONE = 'rest_api.request_model.date_time_transformer_force_local_timezone';
    public const PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_DEFAULT_FORMAT = 'rest_api.request_model.date_time_transformer_default_format';
    public const PARAMETER_REQUEST_MODEL_DATE_TRANSFORMER_DEFAULT_FORMAT = 'rest_api.request_model.date_transformer_default_format';
    public const IS_CONTROLLER_RESPONSE_SUBSCRIBER_ENABLED = 'rest_api.settings.is_controller_response_subscriber_enabled';

    public function getAlias()
    {
        return 'rest_api';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new RestApiBundle\DependencyInjection\Configuration\ConfigExtensionConfiguration($this->getAlias());
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(static::PARAMETER_REQUEST_MODEL_NULLABLE_BY_DEFAULT, $config['request_model']['nullable_by_default']);
        $container->setParameter(static::PARAMETER_REQUEST_MODEL_ALLOW_UNDEFINED_KEYS, $config['request_model']['allow_undefined_keys']);
        $container->setParameter(static::PARAMETER_REQUEST_MODEL_CLEAR_MISSING, $config['request_model']['clear_missing']);
        $container->setParameter(static::PARAMETER_REQUEST_MODEL_HANDLE_EXCEPTION, $config['request_model']['handle_exception']);
        $container->setParameter(static::PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_FORCE_LOCAL_TIMEZONE, $config['request_model']['date_time_transformer_force_local_timezone']);
        $container->setParameter(static::PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_DEFAULT_FORMAT, $config['request_model']['date_time_transformer_default_format']);
        $container->setParameter(static::PARAMETER_REQUEST_MODEL_DATE_TRANSFORMER_DEFAULT_FORMAT, $config['request_model']['date_transformer_default_format']);
        $container->setParameter(static::IS_CONTROLLER_RESPONSE_SUBSCRIBER_ENABLED, $config['settings']['is_controller_response_subscriber_enabled']);
    }
}
