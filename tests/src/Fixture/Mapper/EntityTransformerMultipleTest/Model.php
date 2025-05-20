<?php
declare(strict_types=1);

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

    #[Mapper\FindByField('slug')]
    /**
     * @var Tests\Fixture\TestApp\Entity\Book[]|null
     */
    public ?array $bySlug;
}
