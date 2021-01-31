<?php

namespace TestApp\ResponseModel;

use RestApiBundle;

class CombinedModel implements RestApiBundle\ResponseModelInterface
{
    public function getStringFieldWithTypeHint(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getStringFieldWithDocBlock()
    {
        return '';
    }
}
