<?php

namespace RestApiBundle\Annotation\Docs;

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
