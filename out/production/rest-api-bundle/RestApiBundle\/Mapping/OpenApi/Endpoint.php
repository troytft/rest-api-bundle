<?php

namespace RestApiBundle\Mapping\OpenApi;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Endpoint
{
    /**
     * @Required
     */
    public string $title;
    public ?string $description = null;
    /** @var array<string> */
    public array $tags = [];

    public function __construct(array $options = [], string $title = '', ?string $description = null, array $tags = [])
    {
        $this->title = $options['title'] ?? $title;
        $this->description = $options['description'] ?? $description;
        $this->tags = $options['tags'] ?? $tags;
    }
}
