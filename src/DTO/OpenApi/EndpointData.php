<?php

namespace RestApiBundle\DTO\OpenApi;

use RestApiBundle;
use Symfony\Component\Routing\Annotation\Route;

class EndpointData
{
    /**
     * @var RestApiBundle\Annotation\Docs\Endpoint
     */
    private $endpointAnnotation;

    /**
     * @var Route|null
     */
    private $controllerRouteAnnotation;

    /**
     * @var Route
     */
    private $actionRouteAnnotation;

    /**
     * @var RestApiBundle\DTO\OpenApi\Schema\TypeInterface
     */
    private $returnType;

    /**
     * @var RestApiBundle\DTO\OpenApi\Schema\TypeInterface[]
     */
    private $parameters = [];

    public function getEndpointAnnotation(): RestApiBundle\Annotation\Docs\Endpoint
    {
        return $this->endpointAnnotation;
    }

    public function setEndpointAnnotation(RestApiBundle\Annotation\Docs\Endpoint $endpointAnnotation)
    {
        $this->endpointAnnotation = $endpointAnnotation;

        return $this;
    }

    public function getControllerRouteAnnotation(): ?Route
    {
        return $this->controllerRouteAnnotation;
    }

    public function setControllerRouteAnnotation(?Route $controllerRouteAnnotation)
    {
        $this->controllerRouteAnnotation = $controllerRouteAnnotation;

        return $this;
    }

    public function getActionRouteAnnotation(): Route
    {
        return $this->actionRouteAnnotation;
    }

    public function setActionRouteAnnotation(Route $actionRouteAnnotation)
    {
        $this->actionRouteAnnotation = $actionRouteAnnotation;

        return $this;
    }

    public function getReturnType(): Schema\TypeInterface
    {
        return $this->returnType;
    }

    public function setReturnType(Schema\TypeInterface $returnType)
    {
        $this->returnType = $returnType;

        return $this;
    }

    /**
     * @return RestApiBundle\DTO\OpenApi\Schema\TypeInterface[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\Schema\TypeInterface[] $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }
}
