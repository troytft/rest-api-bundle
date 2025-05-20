<?php
declare(strict_types=1);

namespace RestApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RestApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DependencyInjection\CompilerPass\MapperTransformerCompilerPass());
        $container->registerExtension(new DependencyInjection\SettingsExtension());
        $container->registerExtension(new DependencyInjection\ServicesExtension());
    }
}
