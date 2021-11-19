<?php

namespace RestApiBundle\Model\OpenApi;

use RestApiBundle;
use Symfony\Component\Routing;

class EndpointData
{
    private ?Routing\Annotation\Route $controllerRoute = null;
    private Routing\Annotation\Route $actionRoute;
    private RestApiBundle\Mapping\OpenApi\Endpoint $endpoint;
    private \ReflectionMethod $reflectionMethod;

    private string $path;

    /**
     * @var string[]
     */
    private array $methods;
    private ?RestApiBundle\Model\OpenApi\Request\RequestInterface $request = null;

    /**
     * @var RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface[]
     */
    private array $pathParameters = [];

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param string[] $methods
     * @return $this
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    public function getRequest(): ?RestApiBundle\Model\OpenApi\Request\RequestInterface
    {
        return $this->request;
    }

    public function setRequest(?RestApiBundle\Model\OpenApi\Request\RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface[]
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * @param RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface[] $pathParameters
     *
     * @return $this
     */
    public function setPathParameters(array $pathParameters)
    {
        $this->pathParameters = $pathParameters;

        return $this;
    }

    public function getControllerRoute(): ?Routing\Annotation\Route
    {
        return $this->controllerRoute;
    }

    public function setControllerRoute(?Routing\Annotation\Route $controllerRoute): static
    {
        $this->controllerRoute = $controllerRoute;

        return $this;
    }

    public function getActionRoute(): Routing\Annotation\Route
    {
        return $this->actionRoute;
    }

    public function setActionRoute(Routing\Annotation\Route $actionRoute): static
    {
        $this->actionRoute = $actionRoute;

        return $this;
    }

    public function getEndpoint(): RestApiBundle\Mapping\OpenApi\Endpoint
    {
        return $this->endpoint;
    }

    public function setEndpoint(RestApiBundle\Mapping\OpenApi\Endpoint $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getReflectionMethod(): \ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    public function setReflectionMethod(\ReflectionMethod $reflectionMethod): static
    {
        $this->reflectionMethod = $reflectionMethod;

        return $this;
    }
}
