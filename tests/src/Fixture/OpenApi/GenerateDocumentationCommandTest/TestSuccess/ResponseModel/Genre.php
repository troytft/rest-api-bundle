<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel;

use Tests;
use RestApiBundle;

class Genre implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function __construct(private Tests\Fixture\TestApp\Entity\Book $data)
    {
    }

    public function getId(): int
    {
        return $this->data->getId();
    }
}
