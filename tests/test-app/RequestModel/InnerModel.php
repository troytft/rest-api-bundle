<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel as Mapping;

class InnerModel implements Mapping\RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapping\StringType()
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
