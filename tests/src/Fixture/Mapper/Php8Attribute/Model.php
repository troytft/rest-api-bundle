<?php

namespace Tests\Fixture\Mapper\Php8Attribute;

use RestApiBundle\Mapping\Mapper as Mapper;

class Model implements Mapper\ModelInterface
{
    #[Mapper\AutoType]
    public ?string $name;

    #[Mapper\DateType(format: 'd/m/y')]
    public $date;

    /**
     * @var float|null
     *
     * @Mapper\AutoType
     */
    public $rating;
}
