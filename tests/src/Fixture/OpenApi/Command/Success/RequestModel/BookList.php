<?php

namespace Tests\Fixture\OpenApi\Command\Success\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /** @Mapper\Expose */
    public ?int $offset;

    /** @Mapper\Expose */
    public ?int $limit;

    /**
     * @var string[]|null
     *
     * @Mapper\Expose
     * @Assert\Choice(callback="Tests\Fixture\Commonp\Enum\BookStatus::getValues", multiple=true)
     */
    private ?array $statuses;

    /** @Mapper\Expose */
    public ?Tests\Fixture\Common\Entity\Author $author;

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
