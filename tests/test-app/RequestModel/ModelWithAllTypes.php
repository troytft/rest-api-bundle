<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel;

class ModelWithAllTypes implements RequestModel\RequestModelInterface
{
    /**
     * @var bool|null
     *
     * @RequestModel\BooleanType(nullable=true)
     */
    private $booleanType;

    /**
     * @var float|null
     *
     * @RequestModel\FloatType(nullable=true)
     */
    private $floatType;

    /**
     * @var int|null
     *
     * @RequestModel\IntegerType(nullable=true)
     */
    private $integerType;

    /**
     * @var string|null
     *
     * @RequestModel\StringType(nullable=true)
     */
    private $stringType;

    /**
     * @var InnerModel|null
     *
     * @RequestModel\RequestModelType(class="TestApp\RequestModel\InnerModel", nullable=true)
     */
    private $model;

    /**
     * @var array|null
     *
     * @RequestModel\ArrayType(type=@RequestModel\IntegerType(), nullable=true)
     */
    private $collection;

    /**
     * @var \DateTime|null
     *
     * @RequestModel\DateType(nullable=true)
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @RequestModel\DateTimeType(nullable=true)
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

    public function getModel(): ?InnerModel
    {
        return $this->model;
    }

    public function setModel(?InnerModel $model)
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
