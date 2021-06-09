<?php

namespace Tests\Fixture\Mapper\Php8Attribute;

use RestApiBundle\Mapping\Mapper as Mapper;

class Model implements Mapper\ModelInterface
{
    #[Mapper\StringType(nullable: true)]
    public $name;

    #[Mapper\DateType(format: 'd/m/y')]
    public $date;

    /**
     * @Mapper\FloatType(nullable=true)
     */
    public $rating;
}
