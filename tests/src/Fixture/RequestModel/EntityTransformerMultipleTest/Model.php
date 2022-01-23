<?php

namespace Tests\Fixture\RequestModel\EntityTransformerMultipleTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    public ?array $byId;

    #[Mapper\FindByField('slug')]
    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    public ?array $bySlug;
}
