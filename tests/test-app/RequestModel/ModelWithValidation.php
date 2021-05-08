<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel;
use Symfony\Component\Validator\Constraints as Assert;

class ModelWithValidation implements RequestModel\RequestModelInterface
{
    /**
     * @var string
     *
     * @RequestModel\StringType()
     *
     * @Assert\Length(min=6, max=255, allowEmptyString=false)
     * @Assert\Email()
     */
    private $stringField;

    /**
     * @var InnerModelWithValidation
     *
     * @RequestModel\RequestModelType(class="TestApp\RequestModel\InnerModelWithValidation")
     */
    private $modelField;

    /**
     * @var array
     *
     * @RequestModel\ArrayType(type=@RequestModel\RequestModelType(class="TestApp\RequestModel\InnerModelWithValidation"))
     */
    private $collectionField;

    /**
     * @var int[]
     *
     * @RequestModel\ArrayType(type=@RequestModel\IntegerType())
     *
     * @Assert\All(constraints={
     *     @Assert\Range(min=10)
     * })
     */
    private $collectionOfIntegers;

    /**
     * @return bool
     *
     * @Assert\IsTrue(message="Example message without property")
     */
    public function isValueEquals(): bool
    {
        if ($this->stringField === $this->modelField->getStringField()) {
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

    public function getCollectionOfIntegers(): array
    {
        return $this->collectionOfIntegers;
    }

    public function setCollectionOfIntegers(array $collectionOfIntegers)
    {
        $this->collectionOfIntegers = $collectionOfIntegers;

        return $this;
    }
}
