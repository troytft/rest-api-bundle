<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function (ContainerConfigurator $configurator) {
    $configurator->parameters()
        ->set('kernel.secret', 'docs-secret');

    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->public();
    $services
        ->load('RestApiBundle\\', '../../../src/{Command}/*');
};
