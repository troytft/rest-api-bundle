<?php

namespace Tests\Fixture\Mapper\AutoType;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class AllTypesByDocBlocks implements Mapper\ModelInterface
{
    /**
     * @var string|null
     *
     * @Mapper\AutoType
     */
    public $string;

    /**
     * @var int|null
     *
     * @Mapper\AutoType
     */
    public $int;

    /**
     * @var float|null
     *
     * @Mapper\AutoType
     */
    public $float;

    /**
     * @var bool|null
     *
     * @Mapper\AutoType
     */
    public $bool;

    /**
     * @var string[]|null
     *
     * @Mapper\AutoType
     */
    public $strings;

    /**
     * @var int[]|null
     *
     * @Mapper\AutoType
     */
    public $ints;

    /**
     * @var float[]|null
     *
     * @Mapper\AutoType
     */
    public $floats;

    /**
     * @var bool[]|null
     *
     * @Mapper\AutoType
     */
    public $bools;

    /**
     * @var \DateTime|null
     *
     * @Mapper\AutoType
     */
    public $dateTime;

    /**
     * @var \DateTime[]|null
     *
     * @Mapper\AutoType
     */
    public $dateTimes;

    /**
     * @var Tests\Fixture\Mapper\AutoType\NestedModel|null
     *
     * @Mapper\AutoType
     */
    public $nestedModel;

    /**
     * @var Tests\Fixture\Mapper\AutoType\NestedModel[]|null
     *
     * @Mapper\AutoType
     */
    public $nestedModels;

    /**
     * @var Tests\Fixture\Common\Entity\Author|null
     *
     * @Mapper\AutoType
     */
    public $entity;

    /**
     * @var Tests\Fixture\Common\Entity\Author[]|null
     *
     * @Mapper\AutoType
     */
    public $entities;
}
