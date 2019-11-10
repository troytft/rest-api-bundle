<?php

namespace RestApiBundle\DependencyInjection\CompilerPass;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RequestModelTransformerCompilerPass implements CompilerPassInterface
{
    public const TAG = 'rest_api.request_model_transformer';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RestApiBundle\Manager\RequestModel\RequestModelManager::class)) {
            return;
        }

        $definition = $container->findDefinition(RestApiBundle\Manager\RequestModel\RequestModelManager::class);
        $taggedServices = $container->findTaggedServiceIds(static::TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}
