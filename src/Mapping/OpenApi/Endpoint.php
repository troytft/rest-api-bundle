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
    public string $description;
    /**
     * @var array<string>
     */
    public array $tags = [];
}
