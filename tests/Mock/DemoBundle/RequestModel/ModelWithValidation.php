<?php

namespace Tests\Mock\DemoBundle\RequestModel;

use RestApiBundle\RequestModelInterface;
use RestApiBundle\RequestModel\Annotation as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class ModelWithValidation implements RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\StringType()
     *
     * @Assert\Length(min=6, max=255)
     * @Assert\Email()
     */
    private $stringField;

    /**
     * @var InnerModelWithValidation
     *
     * @Mapper\Model(class="Tests\Mock\DemoBundle\RequestModel\InnerModelWithValidation")
     *
     * @Assert\Valid()
     */
    private $modelField;

    /**
     * @var array
     *
     * @Mapper\Collection(type=@Mapper\Model(class="Tests\Mock\DemoBundle\RequestModel\InnerModelWithValidation"))
     *
     * @Assert\Valid()
     */
    private $collectionField;

    /**
     * @return bool
     *
     * @Assert\IsTrue(message="Example message without property")
     */
    public function isValueEquals(): bool
    {
        if ($this->modelField && $this->stringField === $this->modelField->getStringField()) {
            return false;
        }

        return true;
    }

    public function getStringField(): string
    {
        return $this->stringField;
    }

    public function setStringField(string $stringField)
    {
        $this->stringField = $stringField;

        return $this;
    }

    public function getModelField(): InnerModelWithValidation
    {
        return $this->modelField;
    }

    public function setModelField(InnerModelWithValidation $modelField)
    {
        $this->modelField = $modelField;

        return $this;
    }

    public function getCollectionField(): array
    {
        return $this->collectionField;
    }

    public function setCollectionField(array $collectionField)
    {
        $this->collectionField = $collectionField;

        return $this;
    }
}
