<?php

namespace TestApp\RequestModel;

use RestApiBundle;
use RestApiBundle\Annotation\Request as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class RequestModelForGetRequest implements RestApiBundle\RequestModelInterface
{
    /**
     * @var int
     *
     * @Mapper\IntegerType()
     *
     * @Assert\Range(min=0, max=PHP_INT_MAX)
     * @Assert\NotNull()
     */
    private $offset;

    /**
     * @var int
     *
     * @Mapper\IntegerType()
     *
     * @Assert\Range(min=0, max=20)
     * @Assert\NotNull()
     */
    private $limit;

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }
}
