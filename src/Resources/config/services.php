<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use RestApiBundle;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->public();

    $services
        ->instanceof(RestApiBundle\Services\Mapper\Transformer\TransformerInterface::class)
        ->tag(RestApiBundle\Enum\DependencyInjection\ServiceTag::MAPPER_TRANSFORMER);

    $services
        ->instanceof(ArgumentValueResolverInterface::class)
            ->tag('controller.argument_value_resolver', ['priority' => 25]);

    $services
        ->load('RestApiBundle\\', '../../../src/{EventSubscriber,Services,Command}/*');
};
