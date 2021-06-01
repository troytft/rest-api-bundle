<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\Mapper as Mapper;

class InnerModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\StringType()
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
