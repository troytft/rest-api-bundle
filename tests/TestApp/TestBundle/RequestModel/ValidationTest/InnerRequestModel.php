<?php

namespace Tests\TestApp\TestBundle\RequestModel\ValidationTest;

use RestApiBundle\RequestModelInterface;
use RestApiBundle\Annotation\Request as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class InnerRequestModel implements RequestModelInterface
{
    /**
     * @var string|null
     *
     * @Mapper\StringType(nullable=true)
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
