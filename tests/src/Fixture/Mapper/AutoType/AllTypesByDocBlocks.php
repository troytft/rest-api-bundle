<?php

namespace Tests\Fixture\Mapper\AutoType;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class AllTypesByDocBlocks implements Mapper\ModelInterface
{
    /**
     * @var string|null
     *
     * @Mapper\Field
     */
    public $string;

    /**
     * @var int|null
     *
     * @Mapper\Field
     */
    public $int;

    /**
     * @var float|null
     *
     * @Mapper\Field
     */
    public $float;

    /**
     * @var bool|null
     *
     * @Mapper\Field
     */
    public $bool;

    /**
     * @var string[]|null
     *
     * @Mapper\Field
     */
    public $strings;

    /**
     * @var int[]|null
     *
     * @Mapper\Field
     */
    public $ints;

    /**
     * @var float[]|null
     *
     * @Mapper\Field
     */
    public $floats;

    /**
     * @var bool[]|null
     *
     * @Mapper\Field
     */
    public $bools;

    /**
     * @var \DateTime|null
     *
     * @Mapper\Field
     */
    public $dateTime;

    /**
     * @var \DateTime[]|null
     *
     * @Mapper\Field
     */
    public $dateTimes;

    /**
     * @var Tests\Fixture\Mapper\AutoType\NestedModel|null
     *
     * @Mapper\Field
     */
    public $nestedModel;

    /**
     * @var Tests\Fixture\Mapper\AutoType\NestedModel[]|null
     *
     * @Mapper\Field
     */
    public $nestedModels;

    /**
     * @var Tests\Fixture\Common\Entity\Author|null
     *
     * @Mapper\Field
     */
    public $entity;

    /**
     * @var Tests\Fixture\Common\Entity\Author[]|null
     *
     * @Mapper\Field
     */
    public $entities;
}
