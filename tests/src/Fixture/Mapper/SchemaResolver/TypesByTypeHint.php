<?php

namespace Tests\Fixture\Mapper\SchemaResolver;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class TypesByTypeHint implements Mapper\ModelInterface
{
    #[Mapper\Expose]
    public ?string $string;

    #[Mapper\Expose]
    public ?int $int;

    #[Mapper\Expose]
    public ?float $float;

    #[Mapper\Expose]
    public ?bool $bool;

    #[Mapper\Expose]
    public ?\DateTime $dateTime;

    #[Mapper\Expose]
    #[Mapper\DateFormat('d/m/y H:i:s')]
    public ?\DateTime $dateTimeWithFormat;

    #[Mapper\Expose]
    public ?Tests\Fixture\Mapper\SchemaResolver\NestedModel $nestedModel;

    #[Mapper\Expose]
    public ?Tests\Fixture\Common\Entity\Author $entity;

    #[Mapper\Expose]
    #[Mapper\FindByField('slug')]
    public ?Tests\Fixture\Common\Entity\Book $entityByField;

    #[Mapper\Expose]
    public ?Mapper\Date $date;

    #[Mapper\Expose]
    #[Mapper\DateFormat('d/m/y')]
    public ?Mapper\Date $dateWithFormat;
}
