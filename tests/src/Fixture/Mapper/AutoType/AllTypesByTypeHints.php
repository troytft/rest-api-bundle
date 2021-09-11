<?php

namespace Tests\Fixture\Mapper\AutoType;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class AllTypesByTypeHints implements Mapper\ModelInterface
{
    /** @Mapper\Field */
    public ?string $string;

    /** @Mapper\Field */
    public ?int $int;

    /** @Mapper\Field */
    public ?float $float;

    /** @Mapper\Field */
    public ?bool $bool;

    /** @Mapper\Field */
    public ?\DateTime $dateTime;

    /** @Mapper\Field */
    public ?Tests\Fixture\Mapper\AutoType\NestedModel $nestedModel;

    /** @Mapper\Field */
    public ?Tests\Fixture\Common\Entity\Author $entity;
}
