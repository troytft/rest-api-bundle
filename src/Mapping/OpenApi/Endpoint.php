<?php
declare(strict_types=1);

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
    /** @var string[]|string */
    public $tags;
    public ?string $requestModel;
    public ?int $httpStatusCode;

    /**
     * @param array|string $options
     * @param string[]|string $tags
     */
    public function __construct($options = [], string $title = '', ?string $description = null, $tags = [], ?string $requestModel = null, ?int $httpStatusCode = null)
    {
        if (is_string($options)) {
            $this->title = $options;
            $this->description = $description;
            $this->tags = $tags;
            $this->requestModel = $requestModel;
            $this->httpStatusCode = $httpStatusCode;
        } elseif (is_array($options)) {
            $this->title = $options['title'] ?? $options['value'] ?? $title;
            $this->description = $options['description'] ?? $description;
            $this->tags = $options['tags'] ?? $tags;
            $this->requestModel = $options['requestModel'] ?? $requestModel;
            $this->httpStatusCode = $options['httpStatusCode'] ?? $httpStatusCode;
        } else {
            throw new \InvalidArgumentException();
        }
    }
}
