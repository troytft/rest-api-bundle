<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\Mapper as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class ModelWithValidation implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\Expose
     *
     * @Assert\Length(min=6, max=255, allowEmptyString=false)
     * @Assert\Email()
     */
    private $stringField;

    /**
     * @var TestApp\RequestModel\ValidationTest\InnerModelWithValidation
     *
     * @Mapper\Expose
     */
    private $modelField;

    /**
     * @var TestApp\RequestModel\ValidationTest\InnerModelWithValidation[]
     *
     * @Mapper\Expose
     */
    private $collectionField;

    /**
     * @var int[]
     *
     * @Mapper\Expose
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