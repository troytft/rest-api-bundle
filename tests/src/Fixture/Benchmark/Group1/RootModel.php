<?php

namespace Tests\Fixture\Benchmark\Group1;

use Tests;
use RestApiBundle\Mapping\Mapper;

class RootModel implements Mapper\ModelInterface
{
    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model1 $model1;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model2 $model2;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model3 $model3;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model4 $model4;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model5 $model5;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model6 $model6;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model7 $model7;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model8 $model8;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model9 $model9;

    /** @Mapper\Expose */
    public Tests\Fixture\Benchmark\Group1\Model10 $model10;
}
