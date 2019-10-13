<?php

namespace Tests\Demo\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class InnerModel implements RequestModelInterface
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
