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
     * @var RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface
     */
    private $response;

    /**
     * @var RestApiBundle\DTO\OpenApi\PathParameter[]
     */
    private $pathParameters = [];

    /**
     * @var RestApiBundle\DTO\OpenApi\Schema\ClassType|null
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

    public function getResponse(): Schema\SchemaTypeInterface
    {
        return $this->response;
    }

    public function setResponse(Schema\SchemaTypeInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return RestApiBundle\DTO\OpenApi\PathParameter[]
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\PathParameter[] $pathParameters
     *
     * @return $this
     */
    public function setPathParameters(array $pathParameters)
    {
        $this->pathParameters = $pathParameters;

        return $this;
    }

    public function getRequest(): ?Schema\ClassType
    {
        return $this->request;
    }

    public function setRequest(?Schema\ClassType $request)
    {
        $this->request = $request;

        return $this;
    }
}
