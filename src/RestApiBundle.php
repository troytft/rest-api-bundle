<?php

namespace RestApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RestApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DependencyInjection\CompilerPass\RequestModelTransformerCompilerPass());
        $container->registerExtension(new DependencyInjection\ConfigExtension());
        $container->registerExtension(new DependencyInjection\ServicesExtension());
    }
}
