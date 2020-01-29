<?php

namespace Tests\TestApp\TestBundle\RequestModel;

use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

use Symfony\Component\Validator\Constraints as Assert;

class InnerModelWithValidation implements RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\StringType()
     *
     * @Assert\Length(min=3, max=255)
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
