<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\Mapper as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class RequestModelForGetRequest implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var int
     *
     * @Mapper\Field
     *
     * @Assert\Range(min=0, max=PHP_INT_MAX)
     */
    private $offset;

    /**
     * @var int
     *
     * @Mapper\Field
     *
     * @Assert\Range(min=0, max=20)
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
