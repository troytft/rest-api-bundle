<?php

namespace Tests\Fixture\Mapper\Benchmark;

use Tests;
use RestApiBundle\Mapping\Mapper;

class RootModel implements Mapper\ModelInterface
{
    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group1\RootModel")) */
    public array $group1 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group2\RootModel")) */
    public array $group2 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group3\RootModel")) */
    public array $group3 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group4\RootModel")) */
    public array $group4 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group5\RootModel")) */
    public array $group5 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group6\RootModel")) */
    public array $group6 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group7\RootModel")) */
    public array $group7 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group8\RootModel")) */
    public array $group8 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group9\RootModel")) */
    public array $group9 = [];

    /** @Mapper\ArrayType(type=@Mapper\ModelType(class="Tests\Fixture\Mapper\Benchmark\Group10\RootModel")) */
    public array $group10 = [];
}
