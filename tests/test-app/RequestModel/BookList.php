<?php

namespace TestApp\RequestModel;

use RestApiBundle;
use RestApiBundle\Annotation\Request as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class BookList implements RestApiBundle\RequestModelInterface
{
    /**
     * @var int|null
     *
     * @Mapper\IntegerType(nullable=true)
     */
    private $offset;

    /**
     * @var int|null
     *
     * @Mapper\IntegerType(nullable=true)
     */
    private $limit;

    /**
     * @var string[]|null
     *
     * @Mapper\ArrayType(type=@Mapper\StringType(), nullable=true)
     * @Assert\Choice(callback="TestApp\Enum\BookStatus::getValues", multiple=true)
     */
    private $statuses;

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

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
