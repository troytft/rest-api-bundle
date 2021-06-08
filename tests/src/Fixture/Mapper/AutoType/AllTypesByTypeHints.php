<?php

namespace Tests\Fixture\Mapper\AutoType;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class AllTypesByTypeHints implements Mapper\ModelInterface
{
    /** @Mapper\AutoType */
    public ?string $string;

    /** @Mapper\AutoType */
    public ?int $int;

    /** @Mapper\AutoType */
    public ?float $float;

    /** @Mapper\AutoType */
    public ?bool $bool;

    /** @Mapper\AutoType */
    public ?\DateTime $dateTime;

    /** @Mapper\AutoType */
    public ?Tests\Fixture\Mapper\AutoType\NestedModel $nestedModel;

    /** @Mapper\AutoType */
    public ?Tests\Fixture\Common\Entity\Author $entity;
}
