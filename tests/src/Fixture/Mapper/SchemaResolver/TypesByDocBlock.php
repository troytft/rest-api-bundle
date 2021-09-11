<?php

namespace Tests\Fixture\Mapper\SchemaResolver;

use Tests;
use RestApiBundle\Mapping\Mapper as Mapper;

class TypesByDocBlock implements Mapper\ModelInterface
{
    /**
     * @var string|null
     *
     * @Mapper\Expose
     */
    public $string;

    /**
     * @var int|null
     *
     * @Mapper\Expose
     */
    public $int;

    /**
     * @var float|null
     *
     * @Mapper\Expose
     */
    public $float;

    /**
     * @var bool|null
     *
     * @Mapper\Expose
     */
    public $bool;

    /**
     * @var string[]|null
     *
     * @Mapper\Expose
     */
    public $strings;

    /**
     * @var int[]|null
     *
     * @Mapper\Expose
     */
    public $ints;

    /**
     * @var float[]|null
     *
     * @Mapper\Expose
     */
    public $floats;

    /**
     * @var bool[]|null
     *
     * @Mapper\Expose
     */
    public $bools;

    /**
     * @var \DateTime|null
     *
     * @Mapper\Expose
     */
    public $dateTime;

    /**
     * @var \DateTime[]|null
     *
     * @Mapper\Expose
     */
    public $dateTimes;

    /**
     * @var Tests\Fixture\Mapper\SchemaResolver\NestedModel|null
     *
     * @Mapper\Expose
     */
    public $nestedModel;

    /**
     * @var Tests\Fixture\Mapper\SchemaResolver\NestedModel[]|null
     *
     * @Mapper\Expose
     */
    public $nestedModels;

    /**
     * @var Tests\Fixture\Common\Entity\Author|null
     *
     * @Mapper\Expose
     */
    public $entity;

    /**
     * @var Tests\Fixture\Common\Entity\Author[]|null
     *
     * @Mapper\Expose
     */
    public $entities;

    /**
     * @var Tests\Fixture\Common\Entity\Book|null
     *
     * @Mapper\Expose
     * @Mapper\FindByField("slug")
     */
    public $entityByField;

    /**
     * @var Tests\Fixture\Common\Entity\Book[]|null
     *
     * @Mapper\Expose
     * @Mapper\FindByField("slug")
     */
    public $entitiesByField;
}
