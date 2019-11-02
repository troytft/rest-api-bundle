<?php

namespace Tests\DemoApp\DemoBundle\RequestModel;

use RestApiBundle\Annotation\RequestModel as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithAllTypes implements RequestModelInterface
{
    /**
     * @var bool
     *
     * @Mapper\BooleanType()
     */
    private $booleanType;

    /**
     * @var float
     *
     * @Mapper\FloatType()
     */
    private $floatType;

    /**
     * @var int
     *
     * @Mapper\IntegerType()
     */
    private $integerType;

    /**
     * @var string
     *
     * @Mapper\StringType()
     */
    private $stringType;

    /**
     * @var InnerModel
     *
     * @Mapper\Model(class="Tests\DemoApp\DemoBundle\RequestModel\InnerModel")
     */
    private $model;

    /**
     * @var array
     *
     * @Mapper\Collection(type=@Mapper\IntegerType())
     */
    private $collection;

    /**
     * @var \DateTime
     *
     * @Mapper\Date()
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @Mapper\DateTime()
     */
    private $dateTime;

    public function getBooleanType(): bool
    {
        return $this->booleanType;
    }

    public function setBooleanType(bool $booleanType)
    {
        $this->booleanType = $booleanType;

        return $this;
    }

    public function getFloatType(): float
    {
        return $this->floatType;
    }

    public function setFloatType(float $floatType)
    {
        $this->floatType = $floatType;

        return $this;
    }

    public function getIntegerType(): int
    {
        return $this->integerType;
    }

    public function setIntegerType(int $integerType)
    {
        $this->integerType = $integerType;

        return $this;
    }

    public function getStringType(): string
    {
        return $this->stringType;
    }

    public function setStringType(string $stringType)
    {
        $this->stringType = $stringType;

        return $this;
    }

    public function getModel(): InnerModel
    {
        return $this->model;
    }

    public function setModel(InnerModel $model)
    {
        $this->model = $model;

        return $this;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function setCollection(array $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
