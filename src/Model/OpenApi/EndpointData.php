<?php

declare(strict_types=1);

namespace RestApiBundle\Model\OpenApi;

use RestApiBundle;
use Symfony\Component\Routing;

class EndpointData
{
    public function __construct(
        public \ReflectionMethod $reflectionMethod,
        public RestApiBundle\Mapping\OpenApi\Endpoint $endpointMapping,
        public Routing\Attribute\Route $actionRouteMapping,
        public ?Routing\Attribute\Route $controllerRouteMapping = null,
        public bool $deprecated = false,
    ) {
    }
}
