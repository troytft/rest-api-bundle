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
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var array<string>
     */
    public $tags;
}
