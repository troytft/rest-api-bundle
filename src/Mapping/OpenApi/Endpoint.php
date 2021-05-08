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
     *
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var array<string>
     */
    public $tags = [];
}
