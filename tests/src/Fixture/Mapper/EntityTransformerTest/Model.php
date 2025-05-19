<?php declare(strict_types=1);

namespace Tests\Fixture\Mapper\EntityTransformerTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements Mapper\ModelInterface
{
    #[Mapper\FindByField('slug')]
    public ?Tests\Fixture\TestApp\Entity\Book $bySlug;

    public ?Tests\Fixture\TestApp\Entity\Book $byId;
}
