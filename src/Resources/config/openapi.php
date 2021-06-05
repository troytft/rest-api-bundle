<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function (ContainerConfigurator $configurator) {
    $configurator->parameters()
        ->set('doctrine.dbal.driver', 'pdo_sqlite')
        ->set('doctrine.dbal.memory', true)
        ->set('doctrine.orm.entity_managers.default.auto_mapping', true)
        ->set('framework.translator', null)
        ->set('framework.validation', null)
        ->set('parameters.kernel.secret', 'docs-secret');

    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->public();
    $services
        ->load('RestApiBundle\\', '../../../src/{Command}/*');
};
