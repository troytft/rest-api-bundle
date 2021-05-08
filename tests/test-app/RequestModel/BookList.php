<?php

namespace TestApp\RequestModel;

use RestApiBundle;
use RestApiBundle\Mapping\RequestModel;
use Symfony\Component\Validator\Constraints as Assert;

class BookList implements RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @RequestModel\IntegerType(nullable=true)
     */
    public ?int $offset;

    /**
     * @RequestModel\IntegerType(nullable=true)
     */
    public ?int $limit;

    /**
     * @var string[]|null
     *
     * @RequestModel\ArrayType(type=@RequestModel\StringType(), nullable=true)
     * @Assert\Choice(callback="TestApp\Enum\BookStatus::getValues", multiple=true)
     */
    private $statuses;

    /**
     * @return string[]|null
     */
    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    /**
     * @param string[]|null $statuses
     * @return $this
     */
    public function setStatuses(?array $statuses)
    {
        $this->statuses = $statuses;

        return $this;
    }
}
