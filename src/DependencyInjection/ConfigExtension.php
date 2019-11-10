<?php

namespace RestApiBundle\DependencyInjection;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ConfigExtension extends Extension
{
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new RestApiBundle\DependencyInjection\Configuration\ConfigExtensionConfiguration();
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('rest_api.request_model.nullable_by_default', $config['request_model']['nullable_by_default']);
        $container->setParameter('rest_api.request_model.allow_undefined_keys', $config['request_model']['allow_undefined_keys']);
        $container->setParameter('rest_api.request_model.clear_missing', $config['request_model']['clear_missing']);
        $container->setParameter('rest_api.request_model.handle_mapping_exception', $config['request_model']['handle_mapping_exception']);
    }
}
