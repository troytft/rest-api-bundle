<?php

namespace Tests\Fixture\Mapper\Benchmark;

use Tests;
use RestApiBundle\Mapping\Mapper;

class RootModel implements Mapper\ModelInterface
{
    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group1\RootModel $group1;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group2\RootModel $group2;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group3\RootModel $group3;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group4\RootModel $group4;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group5\RootModel $group5;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group6\RootModel $group6;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group7\RootModel $group7;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group8\RootModel $group8;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group9\RootModel $group9;

    /** @Mapper\AutoType */
    public Tests\Fixture\Mapper\Benchmark\Group10\RootModel $group10;
}
