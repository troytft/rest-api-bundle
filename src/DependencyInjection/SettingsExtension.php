<?php

declare(strict_types=1);

namespace RestApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SettingsExtension extends Extension
{
    public const ALIAS = 'rest_api';

    public function getAlias(): string
    {
        return static::ALIAS;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration\ConfigExtensionConfiguration($this->getAlias());
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias(), $config);
    }
}
