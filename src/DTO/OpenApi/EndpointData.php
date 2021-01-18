<?php

namespace RestApiBundle\DTO\OpenApi;

use RestApiBundle;

class EndpointData
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @var string
     */
    private $routePath;

    /**
     * @var string[]
     */
    private $routeMethods;

    /**
     * @var RestApiBundle\DTO\OpenApi\ResponseInterface
     */
    private $response;

    /**
     * @var RestApiBundle\DTO\OpenApi\PathParameter[]
     */
    private $routePathParameters = [];

    /**
     * @var RestApiBundle\DTO\OpenApi\RequestInterface|null
     */
    private $request;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function setRoutePath(string $routePath)
    {
        $this->routePath = $routePath;

        return $this;
    }

    public function getRouteMethods(): array
    {
        return $this->routeMethods;
    }

    public function setRouteMethods(array $routeMethods)
    {
        $this->routeMethods = $routeMethods;

        return $this;
    }

    public function getResponse(): RestApiBundle\DTO\OpenApi\ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(RestApiBundle\DTO\OpenApi\ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return RestApiBundle\DTO\OpenApi\PathParameter[]
     */
    public function getRoutePathParameters(): array
    {
        return $this->routePathParameters;
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\PathParameter[] $routePathParameters
     *
     * @return $this
     */
    public function setRoutePathParameters(array $routePathParameters)
    {
        $this->routePathParameters = $routePathParameters;

        return $this;
    }

    public function getRequest(): ?RestApiBundle\DTO\OpenApi\RequestInterface
    {
        return $this->request;
    }

    public function setRequest(?RestApiBundle\DTO\OpenApi\RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }
}
