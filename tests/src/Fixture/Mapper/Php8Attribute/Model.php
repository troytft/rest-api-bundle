<?php

namespace Tests\Fixture\Mapper\Php8Attribute;

use RestApiBundle\Mapping\Mapper as Mapper;

class Model implements Mapper\ModelInterface
{
    #[Mapper\Field]
    public ?string $name;

    #[Mapper\DateType(format: 'd/m/y')]
    public $date;

    /**
     * @var float|null
     *
     * @Mapper\Field
     */
    public $rating;
}
