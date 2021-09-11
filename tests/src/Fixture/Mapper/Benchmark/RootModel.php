<?php

namespace Tests\Fixture\Mapper\Benchmark;

use Tests;
use RestApiBundle\Mapping\Mapper;

class RootModel implements Mapper\ModelInterface
{
    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group1\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group1 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group2\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group2 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group3\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group3 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group4\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group4 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group5\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group5 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group6\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group6 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group7\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group7 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group8\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group8 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group9\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group9 = [];

    /**
     * @var Tests\Fixture\Mapper\Benchmark\Group10\RootModel[]
     *
     * @Mapper\Field
     */
    public array $group10 = [];
}
