<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use RestApiBundle;

class ModelWithDocBlock implements RestApiBundle\ResponseModelInterface
{
    /**
     * @return string
     */
    public function getStringField()
    {
        return 'string';
    }

    /**
     * @return string|null
     */
    public function getNullableStringField()
    {
        return null;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeField()
    {
        return new \DateTime();
    }
}
