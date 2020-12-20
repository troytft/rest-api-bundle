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
    private $path;

    /**
     * @var string[]
     */
    private $methods;

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
    private $requestModel;

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

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function setMethods(array $methods)
    {
        $this->methods = $methods;

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

    public function getRequestModel(): ?Schema\ClassType
    {
        return $this->requestModel;
    }

    public function setRequestModel(?Schema\ClassType $requestModel)
    {
        $this->requestModel = $requestModel;

        return $this;
    }
}
