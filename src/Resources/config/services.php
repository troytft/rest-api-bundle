<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use RestApiBundle;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->public()
            ->bind(RestApiBundle\Services\Mapper\SchemaResolverInterface::class, service(RestApiBundle\Services\Mapper\CacheSchemaResolver::class))
            ->bind('$cacheDir', '%kernel.cache_dir%');

    $services
        ->instanceof(RestApiBundle\Services\Mapper\Transformer\TransformerInterface::class)
        ->tag(RestApiBundle\DependencyInjection\CompilerPass\MapperTransformerCompilerPass::TAG);

    $services
        ->instanceof(ArgumentValueResolverInterface::class)
        ->tag('controller.argument_value_resolver', ['priority' => 25]);

    $services
        ->load('RestApiBundle\\EventSubscriber\\', '../../../src/EventSubscriber/*');

    $services
        ->load('RestApiBundle\\Services\\', '../../../src/Services/*');

    $services
        ->load('RestApiBundle\\Services\\OpenApi\\', '../../../src/Services/OpenApi/*')
        ->tag('container.no_preload');

    $services
        ->load('RestApiBundle\\Command\\', '../../../src/Command/*')
        ->tag('container.no_preload');

    $services
        ->load('RestApiBundle\\CacheWarmer\\', '../../../src/CacheWarmer/*')
        ->tag('container.no_preload');
};
