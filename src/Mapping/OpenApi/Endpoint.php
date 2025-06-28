<?php

declare(strict_types=1);

namespace RestApiBundle\Mapping\OpenApi;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Endpoint
{
    /**
     * @param string[]|null $tags
     */
    public function __construct(
        private string $summary,
        private ?string $description = null,
        private ?array $tags = null,
        private ?string $requestModelInterface = null,
        private ?int $httpStatusCode = null,
        private bool $deprecated = false,
    ) {
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function getRequestModelInterface(): ?string
    {
        return $this->requestModelInterface;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    public function getDeprecated(): bool
    {
        return $this->deprecated;
    }
}
