<?php

namespace RestApiBundle\DTO\Docs;

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
     * @var array<string>|null
     */
    private $tags;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array<string>
     */
    private $methods;

    /**
     * @var string|null
     */
    private $responseBody;

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

    /**
     * @return array<string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string>|null $tags
     */
    public function setTags(?array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array<string> $methods
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function setResponseBody(?string $responseBody)
    {
        $this->responseBody = $responseBody;

        return $this;
    }
}
