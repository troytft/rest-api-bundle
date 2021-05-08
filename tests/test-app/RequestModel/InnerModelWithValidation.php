<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel;
use Symfony\Component\Validator\Constraints as Assert;

class InnerModelWithValidation implements RequestModel\RequestModelInterface
{
    /**
     * @var string
     *
     * @RequestModel\StringType()
     *
     * @Assert\Length(min=3, max=255, allowEmptyString=false)
     */
    private $stringField;

    public function getStringField(): string
    {
        return $this->stringField;
    }

    public function setStringField(string $stringField)
    {
        $this->stringField = $stringField;

        return $this;
    }
}
