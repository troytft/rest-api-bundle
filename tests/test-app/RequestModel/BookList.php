<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\Mapper as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @Mapper\IntegerType(nullable=true)
     */
    public ?int $offset;

    /**
     * @Mapper\IntegerType(nullable=true)
     */
    public ?int $limit;

    /**
     * @var string[]|null
     *
     * @Mapper\ArrayType(type=@Mapper\StringType(), nullable=true)
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
