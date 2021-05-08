<?php

namespace TestApp\RequestModel;

use RestApiBundle;
use RestApiBundle\Mapping\RequestModel as Mapping;
use Symfony\Component\Validator\Constraints as Assert;

class BookList implements RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @Mapping\IntegerType(nullable=true)
     */
    public ?int $offset;

    /**
     * @Mapping\IntegerType(nullable=true)
     */
    public ?int $limit;

    /**
     * @var string[]|null
     *
     * @Mapping\ArrayType(type=@Mapping\StringType(), nullable=true)
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
