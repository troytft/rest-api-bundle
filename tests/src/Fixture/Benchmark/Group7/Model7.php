<?php

namespace Tests\Fixture\Benchmark\Group7;

use RestApiBundle\Mapping\Mapper;

class Model7 implements Mapper\ModelInterface
{
    /** @Mapper\Expose */
    public ?string $field1;

    /** @Mapper\Expose */
    public ?string $field2;

    /** @Mapper\Expose */
    public ?string $field3;

    /** @Mapper\Expose */
    public ?string $field4;

    /** @Mapper\Expose */
    public ?string $field5;

    /** @Mapper\Expose */
    public ?string $field6;

    /** @Mapper\Expose */
    public ?string $field7;

    /** @Mapper\Expose */
    public ?string $field8;

    /** @Mapper\Expose */
    public ?string $field9;

    /** @Mapper\Expose */
    public ?string $field10;
}
