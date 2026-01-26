<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;

class WithPublicProperty implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public string $publicProperty = 'value';

    public function getMethodProperty(): string
    {
        return 'methodValue';
    }
}
