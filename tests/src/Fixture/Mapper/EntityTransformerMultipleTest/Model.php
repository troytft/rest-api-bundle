<?php

namespace Tests\Fixture\Mapper\EntityTransformerMultipleTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements Mapper\ModelInterface
{
    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    public ?array $byId;

    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    #[Mapper\FindByField('slug')]
    public ?array $bySlug;
}
