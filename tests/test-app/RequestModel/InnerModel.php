<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel;

class InnerModel implements RequestModel\RequestModelInterface
{
    /**
     * @var string
     *
     * @RequestModel\StringType()
     */
    private $field;

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}
