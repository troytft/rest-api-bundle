<?php

namespace Tests\Fixture\Mapper\AutoType;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class AllSupportedTypes implements Mapper\ModelInterface
{
    /** @Mapper\AutoType */
    public string $string;

    /** @Mapper\AutoType */
    public ?string $nullableString;

    /** @Mapper\AutoType */
    public int $int;

    /** @Mapper\AutoType */
    public ?int $nullableInt;

    /** @Mapper\AutoType */
    public float $float;

    /** @Mapper\AutoType */
    public ?float $nullableFloat;

    /** @Mapper\AutoType */
    public bool $bool;

    /** @Mapper\AutoType */
    public ?bool $nullableBool;

    /** @Mapper\AutoType */
    public \DateTime $dateTime;

    /** @Mapper\AutoType */
    public ?\DateTime $nullableDateTime;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\AutoType\NestedModel $nestedModel;

    /** @Mapper\AutoType */
    public ?Tests\Fixture\Mapper\AutoType\NestedModel $nullableNestedModel;

    /** @Mapper\AutoType */
    public Tests\Fixture\Common\Entity\Author $entity;

    /** @Mapper\AutoType */
    public ?Tests\Fixture\Common\Entity\Author $nullableEntity;
}
