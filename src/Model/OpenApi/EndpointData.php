<?php

namespace RestApiBundle\Model\OpenApi;

use RestApiBundle;
use Symfony\Component\Routing;

class EndpointData
{
    public function __construct(
        public \ReflectionMethod $reflectionMethod,
        public RestApiBundle\Mapping\OpenApi\Endpoint $endpointMapping,
        public Routing\Annotation\Route $actionRouteMapping,
        public ?Routing\Annotation\Route $controllerRouteMapping = null,
    ) {
    }
}
