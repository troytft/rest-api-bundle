<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Mapping\Mapper as Mapper;
use Symfony\Component\Validator\Constraints as Assert;

class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /** @Mapper\Field */
    public ?int $offset;

    /** @Mapper\Field */
    public ?int $limit;

    /**
     * @var string[]|null
     *
     * @Mapper\Field
     * @Assert\Choice(callback="TestApp\Enum\BookStatus::getValues", multiple=true)
     */
    private ?array $statuses;

    /** @Mapper\Field */
    public ?TestApp\Entity\Author $author;

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
