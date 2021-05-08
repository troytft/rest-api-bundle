<?php

namespace TestApp\RequestModel\ValidationTest;

use RestApiBundle\Mapping\RequestModel;
use Symfony\Component\Validator\Constraints as Assert;

class InnerRequestModel implements RequestModel\RequestModelInterface
{
    /**
     * @var string|null
     *
     * @RequestModel\StringType(nullable=true)
     *
     * @Assert\Expression(expression="false", message="Invalid value.")
     */
    private $field;

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field)
    {
        $this->field = $field;

        return $this;
    }
}
