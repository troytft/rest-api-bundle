<?php

namespace RestApiBundle\Model\OpenApi;

use RestApiBundle;
use Symfony\Component\Routing;

class EndpointData
{
    public ?Routing\Annotation\Route $controllerRouteMapping = null;
    public Routing\Annotation\Route $actionRouteMapping;
    public RestApiBundle\Mapping\OpenApi\Endpoint $endpointMapping;
    public \ReflectionMethod $reflectionMethod;
}
