<?php

namespace RestApiBundle\Mapping\OpenApi;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Endpoint
{
    /**
     * @Required
     */
    public string $title;
    public ?string $description = null;
    /**
     * @var array<string>
     */
    public array $tags = [];
}
