<?php

namespace Tests\Fixture\RequestModel\EntityTransformerTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    #[Mapper\FindByField('slug')]
    public ?Tests\Fixture\TestApp\Entity\Book $bySlug;

    public ?Tests\Fixture\TestApp\Entity\Book $byId;
}
