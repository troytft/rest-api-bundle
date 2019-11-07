<?php

namespace RestApiBundle\DependencyInjection;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ConfigExtension extends Extension
{
    public function getAlias(): string
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
        $container->setParameter($this->getAlias(), $config);
    }
}
