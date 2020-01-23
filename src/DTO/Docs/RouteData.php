<?php

namespace RestApiBundle\DTO\Docs;

use RestApiBundle;

class RouteData
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
     * @var RestApiBundle\DTO\Docs\Type\TypeInterface
     */
    private $returnType;

    /**
     * @var RestApiBundle\DTO\Docs\PathParameter[]
     */
    private $pathParameters = [];

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

    public function getReturnType(): Type\TypeInterface
    {
        return $this->returnType;
    }

    public function setReturnType(Type\TypeInterface $returnType)
    {
        $this->returnType = $returnType;

        return $this;
    }

    /**
     * @return RestApiBundle\DTO\Docs\PathParameter[]
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    public function addPathParameter(RestApiBundle\DTO\Docs\PathParameter $pathParameter)
    {
        $this->pathParameters[] = $pathParameter;
    }
}
