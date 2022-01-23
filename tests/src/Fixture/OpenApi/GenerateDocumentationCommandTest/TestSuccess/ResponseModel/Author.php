<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel;

use Tests;
use RestApiBundle;

class Author implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getId(): int
    {
        return 0;
    }
}
