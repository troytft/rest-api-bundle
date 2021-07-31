<?php

namespace Tests\Fixture\RequestModel;

use RestApiBundle\Mapping\Mapper as Mapper;

class InnerModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\AutoType
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
