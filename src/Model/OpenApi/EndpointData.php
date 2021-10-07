<?php

namespace RestApiBundle\Model\OpenApi;

use RestApiBundle;

class EndpointData
{
    private string $title;
    private ?string $description = null;

    /**
     * @var string[]
     */
    private ?array $tags;
    private string $path;

    /**
     * @var string[]
     */
    private array $methods;
    private ?RestApiBundle\Model\OpenApi\Request\RequestInterface $request = null;
    private RestApiBundle\Model\OpenApi\Response\ResponseInterface $response;

    /**
     * @var RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface[]
     */
    private array $pathParameters = [];

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

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags)
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

    public function getResponse(): RestApiBundle\Model\OpenApi\Response\ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(RestApiBundle\Model\OpenApi\Response\ResponseInterface $response)
    {
        $this->response = $response;

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
}
