<?php

namespace TestApp\ResponseModel;

use RestApiBundle;

class ModelWithInvalidReturnType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    /**
     * @return string|string
     */
    public function getStringField()
    {
        return 'string';
    }
}
