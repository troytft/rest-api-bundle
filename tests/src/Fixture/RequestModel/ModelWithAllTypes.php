<?php

namespace Tests\Fixture\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class ModelWithAllTypes implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var bool|null
     *
     * @Mapper\BooleanType(nullable=true)
     */
    private $booleanType;

    /**
     * @var float|null
     *
     * @Mapper\FloatType(nullable=true)
     */
    private $floatType;

    /**
     * @var int|null
     *
     * @Mapper\IntegerType(nullable=true)
     */
    private $integerType;

    /**
     * @var string|null
     *
     * @Mapper\StringType(nullable=true)
     */
    private $stringType;

    /**
     * @var Tests\Fixture\RequestModel\InnerModel|null
     *
     * @Mapper\ModelType(class="Tests\Fixture\RequestModel\NestedModel", nullable=true)
     */
    private $model;

    /**
     * @var array|null
     *
     * @Mapper\ArrayType(type=@Mapper\IntegerType(), nullable=true)
     */
    private $collection;

    /**
     * @var \DateTime|null
     *
     * @Mapper\DateType(nullable=true)
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @Mapper\DateTimeType(nullable=true)
     */
    private $dateTime;

    public function getBooleanType(): ?bool
    {
        return $this->booleanType;
    }

    public function setBooleanType(?bool $booleanType)
    {
        $this->booleanType = $booleanType;

        return $this;
    }

    public function getFloatType(): ?float
    {
        return $this->floatType;
    }

    public function setFloatType(?float $floatType)
    {
        $this->floatType = $floatType;

        return $this;
    }

    public function getIntegerType(): ?int
    {
        return $this->integerType;
    }

    public function setIntegerType(?int $integerType)
    {
        $this->integerType = $integerType;

        return $this;
    }

    public function getStringType(): ?string
    {
        return $this->stringType;
    }

    public function setStringType(?string $stringType)
    {
        $this->stringType = $stringType;

        return $this;
    }

    public function getModel(): ?Tests\Fixture\RequestModel\InnerModel
    {
        return $this->model;
    }

    public function setModel(?Tests\Fixture\RequestModel\InnerModel $model)
    {
        $this->model = $model;

        return $this;
    }

    public function getCollection(): ?array
    {
        return $this->collection;
    }

    public function setCollection(?array $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
