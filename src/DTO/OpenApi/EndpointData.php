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
     * @var RestApiBundle\DTO\OpenApi\Request\RequestInterface|null
     */
    private $request;

    /**
     * @var RestApiBundle\DTO\OpenApi\Response\ResponseInterface
     */
    private $response;

    /**
     * @var RestApiBundle\DTO\OpenApi\PathParameter[]
     */
    private $routePathParameters = [];

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return $this
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     *
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    /**
     * @return $this
     */
    public function setRoutePath(string $routePath)
    {
        $this->routePath = $routePath;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRouteMethods(): array
    {
        return $this->routeMethods;
    }

    /**
     * @param string[] $routeMethods
     *
     * @return $this
     */
    public function setRouteMethods(array $routeMethods)
    {
        $this->routeMethods = $routeMethods;

        return $this;
    }

    public function getResponse(): RestApiBundle\DTO\OpenApi\Response\ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return $this
     */
    public function setResponse(RestApiBundle\DTO\OpenApi\Response\ResponseInterface $response)
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

    public function getRequest(): ?RestApiBundle\DTO\OpenApi\Request\RequestInterface
    {
        return $this->request;
    }

    /**
     * @return $this
     */
    public function setRequest(?RestApiBundle\DTO\OpenApi\Request\RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }
}
