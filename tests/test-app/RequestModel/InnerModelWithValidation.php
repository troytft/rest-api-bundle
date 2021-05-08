<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel as Mapping;
use Symfony\Component\Validator\Constraints as Assert;

class InnerModelWithValidation implements Mapping\RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapping\StringType()
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
