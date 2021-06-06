<?php

namespace Tests\Fixture\Mapper\Benchmark\Group9;

use Tests;
use RestApiBundle\Mapping\Mapper;

class RootModel implements Mapper\ModelInterface
{
    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model1 $model1;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model2 $model2;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model3 $model3;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model4 $model4;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model5 $model5;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model6 $model6;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model7 $model7;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model8 $model8;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model9 $model9;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\Model10 $model10;
}
