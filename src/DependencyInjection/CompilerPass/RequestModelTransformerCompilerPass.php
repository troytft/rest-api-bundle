<?php

namespace RestApiBundle\DependencyInjection\CompilerPass;

use RestApiBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RequestModelTransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RestApiBundle\Services\Mapper\Mapper::class)) {
            return;
        }

        $definition = $container->findDefinition(RestApiBundle\Services\Mapper\Mapper::class);
        $taggedServices = $container->findTaggedServiceIds(RestApiBundle\Enum\DependencyInjection\ServiceTag::MAPPER_TRANSFORMER);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}
