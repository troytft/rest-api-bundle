<?php

namespace RestApiBundle\Annotation\Docs;

use function var_dump;

/**
 * @Annotation
 */
class Endpoint
{
    private $name;
    private $description;
    private $tags;

    public function __construct(array $values)
    {
        var_dump($values);
    }
}
