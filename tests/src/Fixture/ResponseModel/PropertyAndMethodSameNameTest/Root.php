<?php

namespace Tests\Fixture\ResponseModel\PropertyAndMethodSameNameTest;

use RestApiBundle;
use Tests;

class Root implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    /**
     * @param Tests\Fixture\TestApp\Entity\Book[] $items
     */
    public function __construct(array $items)
    {
    }

    /**
     * @return Tests\Fixture\ResponseModel\PropertyAndMethodSameNameTest\Inner[]
     */
    public function getItems(): array
    {
        return [];
    }
}
