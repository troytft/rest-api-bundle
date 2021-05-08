<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel as Mapping;

class ModelWithAllTypes implements Mapping\RequestModelInterface
{
    /**
     * @var bool|null
     *
     * @Mapping\BooleanType(nullable=true)
     */
    private $booleanType;

    /**
     * @var float|null
     *
     * @Mapping\FloatType(nullable=true)
     */
    private $floatType;

    /**
     * @var int|null
     *
     * @Mapping\IntegerType(nullable=true)
     */
    private $integerType;

    /**
     * @var string|null
     *
     * @Mapping\StringType(nullable=true)
     */
    private $stringType;

    /**
     * @var InnerModel|null
     *
     * @Mapping\RequestModelType(class="TestApp\RequestModel\InnerModel", nullable=true)
     */
    private $model;

    /**
     * @var array|null
     *
     * @Mapping\ArrayType(type=@Mapping\IntegerType(), nullable=true)
     */
    private $collection;

    /**
     * @var \DateTime|null
     *
     * @Mapping\DateType(nullable=true)
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @Mapping\DateTimeType(nullable=true)
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
