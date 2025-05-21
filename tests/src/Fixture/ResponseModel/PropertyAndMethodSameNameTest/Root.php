<?php

namespace Tests\Fixture\ResponseModel\PropertyAndMethodSameNameTest;

use RestApiBundle;
use Tests;

class Root implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]
     */
    private array $items;

    /**
     * @param Tests\Fixture\TestApp\Entity\Book[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return Inner[]
     */
    public function getItems(): array
    {
        return [];
    }
}
