<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use RestApiBundle;

class TestModelWithDateTime implements RestApiBundle\ResponseModelInterface
{
    public function getDateTime(): \DateTime
    {
        return new \DateTime('now');
    }
}
