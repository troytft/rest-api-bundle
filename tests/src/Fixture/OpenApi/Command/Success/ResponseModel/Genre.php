<?php

namespace Tests\Fixture\OpenApi\Command\Success\ResponseModel;

use Tests;
use RestApiBundle;

class Genre implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function __construct(private Tests\Fixture\Common\Entity\Book $data)
    {
    }

    public function getId(): int
    {
        return $this->data->getId();
    }
}