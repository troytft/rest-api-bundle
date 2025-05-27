<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;

class Inner implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getStringField(): string
    {
        return '';
    }
}
