<?php

declare(strict_types=1);

namespace RestApiBundle\DependencyInjection\CompilerPass;

use RestApiBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MapperTransformerCompilerPass implements CompilerPassInterface
{
    public const TAG = 'rest_api.mapper.transformer';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RestApiBundle\Services\Mapper\Mapper::class)) {
            return;
        }

        $definition = $container->findDefinition(RestApiBundle\Services\Mapper\Mapper::class);
        $taggedServices = $container->findTaggedServiceIds(static::TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}
