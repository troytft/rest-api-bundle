<?php

namespace TestApp\RequestModel\ValidationTest;

use RestApiBundle\Mapping\RequestModel as Mapping;
use Symfony\Component\Validator\Constraints as Assert;

class InnerRequestModel implements Mapping\RequestModelInterface
{
    /**
     * @var string|null
     *
     * @Mapping\StringType(nullable=true)
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
