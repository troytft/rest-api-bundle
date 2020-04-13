<?php

namespace Tests\TestApp\TestBundle\RequestModel;

use RestApiBundle;
use RestApiBundle\Annotation\Request as Mapper;

class RequestModelForGetRequest implements RestApiBundle\RequestModelInterface
{
    /**
     * @var int
     *
     * @Mapper\IntegerType()
     */
    private $offset;

    /**
     * @var int
     *
     * @Mapper\IntegerType()
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
