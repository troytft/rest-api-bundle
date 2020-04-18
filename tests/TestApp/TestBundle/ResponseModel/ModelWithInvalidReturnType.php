<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use Tests;
use RestApiBundle;

class ModelWithInvalidReturnType implements RestApiBundle\ResponseModelInterface
{
    /**
     * @return string|string
     */
    public function getStringField()
    {
        return 'string';
    }
}
