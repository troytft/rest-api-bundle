<?php

namespace RestApiBundle\Mapping\OpenApi;

use function is_array;
use function is_string;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Endpoint
{
    /**
     * @Required
     */
    public string $title;
    public ?string $description;
    /** @var array<string>|string */
    public array $tags;

    /**
     * @param array|string $options
     * @param string[]|string $tags
     */
    public function __construct($options = [], string $title = '', ?string $description = null, $tags = [])
    {
        if (is_string($options)) {
            $this->title = $options;
            $this->description = $description;
            $this->tags = $this->normalizeTags($tags);
        } elseif (is_array($options)) {
            $this->title = $options['title'] ?? $options['value'] ?? $title;
            $this->description = $options['description'] ?? $description;
            $this->tags = $this->normalizeTags($options['tags'] ?? $tags);
        } else {
            throw new \InvalidArgumentException();
        }
    }

    private function normalizeTags($value): array
    {
        if (is_string($value)) {
            return [$value];
        } elseif (is_array($value)) {
            return $value;
        } else {
            throw new \InvalidArgumentException();
        }
    }
}
